/**
 * A refined, readable version of the Mmenu Light logic.
 * Handles responsive navigation and off-canvas drawer mechanics.
 */

class MmenuLight {
    constructor(menuElement, breakpoint = "all") {
        this.menu = menuElement;
        this.toggler = new ResponsiveMediaQuery(breakpoint);
        this.navigator = null;
        this.drawer = null;
    }

    // 1. Setup the Navigation Logic (Submenus, Titles, Themes)
    setupNavigation(options = {}) {
        const {
            title = "Menu",
            selectedClass = "Selected",
            slidingSubmenus = true,
            theme = "light"
        } = options;

        this.navigator = new MenuNavigator(this.menu, title, selectedClass, slidingSubmenus, theme);

        // Toggle the 'mm-spn' class based on media query matches
        this.toggler.add(
            () => this.menu.classList.add(this.navigator.prefix),
            () => this.menu.classList.remove(this.navigator.prefix)
        );

        return this.navigator;
    }

    // 2. Setup the Off-Canvas Wrapper (The Drawer)
    setupOffcanvas(options = {}) {
        const { position = "left" } = options;
        
        this.drawer = new OffcanvasDrawer(null, position);
        
        // Create a placeholder to remember where the menu was in the DOM
        const placeholder = document.createComment("original menu location");
        this.menu.after(placeholder);

        this.toggler.add(
            () => this.drawer.content.append(this.menu), // Move into drawer on mobile
            () => {
                this.drawer.close();
                placeholder.after(this.menu); // Move back to original spot on desktop
            }
        );

        return this.drawer;
    }
}

// --- Internal Helper Classes ---

class ResponsiveMediaQuery {
    constructor(query) {
        this.matchFns = [];
        this.unmatchFns = [];
        this.toggler = window.matchMedia(query);
        
        this.listener = (event) => {
            (event.matches ? this.matchFns : this.unmatchFns).forEach(fn => fn());
        };
        
        this.toggler.addListener(this.listener);
    }

    add(matchFns, unmatchFns) {
        this.matchFns.push(matchFns);
        this.unmatchFns.push(unmatchFns);
        (this.toggler.matches ? matchFns : unmatchFns)();
    }
}

class MenuNavigator {
    constructor(node, title, selectedClass, slidingSubmenus, theme) {
        this.node = node;
        this.title = title;
        this.selectedClass = selectedClass;
        this.slidingSubmenus = slidingSubmenus;
        this.prefix = "mm-spn";

        // IE11 Check: Disable sliding submenus for compatibility
        const isIE = navigator.userAgent.indexOf("MSIE") > -1 || navigator.appVersion.indexOf("Trident/") > -1;
        if (isIE) this.slidingSubmenus = false;

      if (this.node) {
            this.node.classList.add(this.prefix);
            this.node.classList.add(`${this.prefix}--${theme}`);
            this.node.classList.add(`${this.prefix}--${this.slidingSubmenus ? "navbar" : "vertical"}`);
        }

        this.initSelected();
        this.initClickEvents();
    }

    openPanel(panel) {
        const parent = panel.parentElement;
        
        if (this.slidingSubmenus) {
            // Handle sliding logic and breadcrumb titles
            let panelTitle = panel.dataset.mmSpnTitle;
            if (parent === this.node) {
                this.node.classList.add(`${this.prefix}--main`);
            } else {
                this.node.classList.remove(`${this.prefix}--main`);
                if (!panelTitle) {
                    Array.from(parent.children).forEach(child => {
                        if (child.matches("a, span")) panelTitle = child.textContent;
                    });
                }
            }
            
            this.node.dataset.mmSpnTitle = panelTitle || this.title;
            
            // Toggle visibility classes
            this.node.querySelectorAll(".mm-spn--open").forEach(el => {
                el.classList.remove(`${this.prefix}--open`, `${this.prefix}--parent`);
            });
            
            panel.classList.add(`${this.prefix}--open`);
            
            let ancestor = panel.parentElement.closest("ul");
            while (ancestor) {
                ancestor.classList.add(`${this.prefix}--open`, `${this.prefix}--parent`);
                ancestor = ancestor.parentElement.closest("ul");
            }
        }
    }

    _initSelected() {
        const selected = this.node.querySelectorAll(`.${this.selectedClass}`);
        const lastSelected = selected[selected.length - 1];
        const panel = lastSelected ? lastSelected.closest("ul") : this.node.querySelector("ul");
        if (panel) this.openPanel(panel);
    }

    _initClickEvents() {
        this.node.addEventListener("click", (e) => {
            const target = e.target;
            // Simplified logic: Check if we clicked a link or a span that opens a menu
            if (target.matches("a") || target.closest("span") || target.closest("li")) {
                // Determine if we should open a panel based on children
                const item = target.closest("li");
                if (item) {
                    Array.from(item.children).forEach(child => {
                        if (child.matches("ul")) this.openPanel(child);
                    });
                }
            }
        });
    }
}

class OffcanvasDrawer {
    constructor(content, position) {
        this.wrapper = document.createElement("div");
        this.wrapper.classList.add("mm-ocd", `mm-ocd--${position}`);

        this.content = document.createElement("div");
        this.content.classList.add("mm-ocd__content");
        
        this.backdrop = document.createElement("div");
        this.backdrop.classList.add("mm-ocd__backdrop");

        this.wrapper.append(this.content, this.backdrop);
        document.body.append(this.wrapper);

        const closeBtn = (e) => {
            this.close();
            e.stopImmediatePropagation();
        };

        this.backdrop.addEventListener("mousedown", closeBtn);
        this.backdrop.addEventListener("touchstart", closeBtn, { passive: true });
    }

    open() {
        this.wrapper.classList.add("mm-ocd--open");
        document.body.classList.add("mm-ocd-opened");
    }

    close() {
        this.wrapper.classList.remove("mm-ocd--open");
        document.body.classList.remove("mm-ocd-opened");
    }
}

// Global Export
window.MmenuLight = MmenuLight;