document.addEventListener("DOMContentLoaded", function () {

    // SIDEBAR TOGGLE
    const sidebarToggle = document.getElementById("sidebarToggle");

    if (sidebarToggle) {
        sidebarToggle.addEventListener("click", function () {
            document.body.classList.toggle("sidebar-collapsed");
            const isCollapsed = document.body.classList.contains("sidebar-collapsed");
            sidebarToggle.setAttribute("aria-expanded", !isCollapsed);
            sidebarToggle.setAttribute(
                "aria-label",
                isCollapsed ? "Expand sidebar" : "Collapse sidebar"
            );
        });
    }


    // CALENDAR PROJECT HOVER DETAILS
    const calendarGrid = document.querySelector(".calendar-grid");

    if (calendarGrid) {

        // Consume events injected from the PHP backend via window.projectEvents
        const projectEvents = window.projectEvents || {};

        // CREATE TOOLTIP
        const tooltip = document.createElement("div");
        tooltip.className = "calendar-project-tooltip";
        tooltip.hidden = true;
        tooltip.setAttribute("role", "tooltip");
        document.body.appendChild(tooltip);

        let activeDay = null;


        // POSITION TOOLTIP
        function positionTooltip(day) {
            const rect = day.getBoundingClientRect();
            const tipRect = tooltip.getBoundingClientRect();
            const gap = 8;

            let left = Math.min(
                Math.max(12, rect.left),
                window.innerWidth - tipRect.width - 12
            );

            let top = rect.bottom + gap;

            if (top + tipRect.height > window.innerHeight - 12) {
                top = rect.top - tipRect.height - gap;
            }

            tooltip.style.left = `${left}px`;
            tooltip.style.top = `${Math.max(12, top)}px`;
        }


        // SHOW TOOLTIP
        function showTooltip(day) {
            const events = projectEvents[day.dataset.date];

            if (!events) return;

            activeDay = day;

            const date = new Date(`${day.dataset.date}T12:00:00`);
            const dateLabel = date.toLocaleDateString("en", {
                month: "long",
                day: "numeric",
                year: "numeric"
            });

            tooltip.innerHTML =
                `<p class="tooltip-date">${dateLabel} · Scheduled work</p>` +
                events.map(event => `
                    <div class="tooltip-project">
                        <strong>${event.name}</strong>
                        <span class="tooltip-meta">
                            ${event.id} · ${event.client}<br>
                            ${event.type}
                        </span>
                        <div class="tooltip-phase">
                            <span>${event.phase}</span>
                            <b>${event.progress}%</b>
                        </div>
                        <div class="tooltip-progress">
                            <i style="width:${event.progress}%"></i>
                        </div>
                    </div>
                `).join("");

            tooltip.hidden = false;
            positionTooltip(day);
        }


        // HIDE TOOLTIP
        function hideTooltip() {
            tooltip.hidden = true;
            activeDay = null;
        }


        // ATTACH EVENTS TO SCHEDULED DATES
        calendarGrid.querySelectorAll(".day.has-event").forEach(day => {
            day.addEventListener("mouseenter", () => { showTooltip(day); });
            day.addEventListener("mouseleave", () => {
                if (!tooltip.matches(":hover")) { hideTooltip(); }
            });
            day.addEventListener("focus", () => { showTooltip(day); });
            day.addEventListener("blur", hideTooltip);
            day.addEventListener("click", event => {
                event.stopPropagation();
                if (activeDay === day && !tooltip.hidden) {
                    hideTooltip();
                } else {
                    showTooltip(day);
                }
            });
        });


        // TOOLTIP INTERACTIONS
        tooltip.addEventListener("mouseleave", hideTooltip);

        document.addEventListener("click", event => {
            if (!tooltip.contains(event.target) && !event.target.closest(".day.has-event")) {
                hideTooltip();
            }
        });


        // REPOSITION TOOLTIP
        window.addEventListener("resize", () => {
            if (activeDay && !tooltip.hidden) { positionTooltip(activeDay); }
        });

        window.addEventListener("scroll", () => {
            if (activeDay && !tooltip.hidden) { positionTooltip(activeDay); }
        }, true);

    }

});