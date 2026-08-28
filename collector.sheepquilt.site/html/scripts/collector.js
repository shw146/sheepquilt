(function() {
    'use strict';
    const endpoint = '/collect';

    const MAX_ERRORS = 10;
    let errorCount = 0;
    const reportedErrors = new Set();


    // Check for user id inside of localstorage. If it doesn't exist, generate one and add it to localstorage
    let userId = localStorage.getItem("user_id");

    if (!userId) {
        userId = crypto.randomUUID();
        localStorage.setItem("user_id", userId);
    }




    // Collect all of the static information
    function getStaticInfo(){
        // Make a link to the connection object to grab effective connection
        const connection = navigator.connection;

        // Try loading an image. If it loads, then images are enabled. Otherwise, images may not be enabled
        const img = new Image();
        let imagesEnabled;
        img.onload = function(){
            imagesEnabled = true;
        }
        img.onerror = function(){
            imagesEnabled = "images may be disabled or the image could not be loaded";
        }

        // Try applying css to an element that does not show up on the document. If it loads, then css is enabled. Otherwise, it may not be
        const test = document.createElement("div");
        let CSSEnabled;

        test.className = "css-test";
        document.body.appendChild(test);

        const styles = getComputedStyle(test);

        if (styles.display === "none") {
            CSSEnabled = true;
        } else {
            CSSEnabled = false;
        }

        test.remove();
        return{
            currentPage: window.location.href,
            currentTime: now,
            ua: navigator.userAgent,
            language: navigator.language,
            screenWidth: window.screen.width,
            screenHeight: window.screen.height,
            windowWidth: window.innerWidth,
            windowHeight: window.innerHeight,
            networkType: connection.effectiveType,
            cookiesEnabled: navigator.cookieEnabled,
            JSEnabled: true,
            imagesEnabled: imagesEnabled,
            CSSEnabled: CSSEnabled
        }
    }

    // Collect all of the performance information
    function getPerformanceInfo(){
        // Set up to grab navigation information
        const entries = performance.getEntriesByType('navigation');
        if (!entries.length) return {};

        const n = entries[0];
        
        return{
            loadStart: round(n.responseStart),
            loadEnd: round(n.responseEnd),
            loadTime: round(n.responseEnd-n.reponseStart),
        }
    }

    // Round function to deal with ms measurements
    function round(n) {
        return Math.round(n * 100) / 100;
    }
    
    
    // Aggregate all of the information, then send to endpoint
    function collect(data) {
        let payload;
        if(data == null){
            payload = {
                uuid: userId,
                url: window.location.href,
                title: document.title,
                referrer: document.referrer,
                timestamp: new Date().toISOString(),
                type: 'pageview',
                staticInfo: getStaticInfo(),
                performanceInfo: getPerformanceInfo(),
            };
        }else{
            payload = {
                uuid: userId,
                url: window.location.href,
                title: document.title,
                referrer: document.referrer,
                timestamp: new Date().toISOString(),
                type: 'pageview',
                staticInfo: getStaticInfo(),
                performanceInfo: getPerformanceInfo(),
                data: data,
            }
        }

        // Log to console so you can see what would be sent
        console.log('Sending beacon:', payload);

        const blob = new Blob([JSON.stringify(payload)], {type: 'application/json'});

        if (navigator.sendBeacon) {
            const sent = navigator.sendBeacon(endpoint, blob);
            console.log('sendBeacon returned:', sent);
        } else {
            console.log('sendBeacon not available, using fetch fallback');
            fetch(endpoint, {
                method: 'POST',
                body: blob,
                keepalive: true
            }).catch((err) => {
                console.log('fetch fallback error:', err.message);
            });
        }
    }

    // Fire on page load
    if (document.readyState === 'complete') {
        collect(null);
    } else {
        window.addEventListener('load', collect);
    }

    // Fire on page exit
    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'hidden') {
            collect(null);
        }
    });

    // Track errors
    function initErrorTracking(){
        window.addEventListener('error', (event) => {
            if (event instanceof ErrorEvent) {
                // JavaScript runtime error
                reportError({
                type: 'js-error',
                message: event.message,
                source: event.filename,
                line: event.lineno,
                column: event.colno,
                stack: event.error ? event.error.stack : '',
                url: window.location.href
                });
            } else {
                // Resource load failure (IMG, SCRIPT, LINK)
                const target = event.target;
                if (target && (target.tagName === 'IMG' || target.tagName === 'SCRIPT' || target.tagName === 'LINK')) {
                    reportError({
                        type: 'resource-error',
                        tagName: target.tagName,
                        src: target.src || target.href || '',
                        url: window.location.href
                    });
                }
            }
        }, true); // capture phase required for resource errors

        // Unhandled promise rejections
        window.addEventListener('unhandledrejection', (event) => {
            const reason = event.reason;
            reportError({
                type: 'promise-rejection',
                message: reason instanceof Error ? reason.message : String(reason),
                stack: reason instanceof Error ? reason.stack : '',
                url: window.location.href
            });
        });
        console.log('Error tracking initialized');
    }

    // Send errors 
    function reportError(errorData) {
        // Rate limit: max errors per page load
        if (errorCount >= MAX_ERRORS) {
            console.log(`Error rate limit reached (${MAX_ERRORS}), ignoring:`, errorData.message);
            return;
        }

        // Deduplicate by type + message + source + line
        const key = `${errorData.type}:${errorData.message || ''}:${errorData.source || ''}:${errorData.line || ''}`;
        if (reportedErrors.has(key)) {
            console.log('Duplicate error suppressed:', errorData.message);
        return;
        }
        reportedErrors.add(key);
        errorCount++;

        console.log(`Error #${errorCount}:`, errorData.type, '-', errorData.message);

        // Send error beacon
        const payload = {
        type: 'error',
        error: errorData,
        timestamp: new Date().toISOString(),
        url: window.location.href,
        session: getSessionId()
        };

        collect(payload);

        // Dispatch custom event so test pages can display the error
        window.dispatchEvent(new CustomEvent('collector:error', { detail: { errorData: errorData, count: errorCount } }));
    }


    // Track mouse inputs
    function initMouseTracking(){
        let mouseData = {
            mouseX: 0,
            mouseY: 0,
            clickX: null,
            clickY: null,
            clickType: null,
            scrollX: window.scrollX,
            scrollY: window.scrollY
        };
        // movement
        document.addEventListener("mousemove", (event) => {
            reportMouse({
                mouseX: event.clientX,
                mouseY: event.clientY
            })
        });
        // click
        document.addEventListener("click", (event) => {
            reportMouse({
                clickX: event.clientX,
                clickY: event.clientY,
                clickType: event.button
            })
        });
        // scroll
        window.addEventListener("scroll", () => {
            reportMouse({
                scrollX: window.scrollX,
                scrollY: window.scrollY
            })
        });
    }

    function reportMouse(mouseData){
        const payload = {
        type: 'mouse',
        data: mouseData
        };

        collect(payload);
    }

    function initKeyboardTracking(){
        window.addEventListener("keydown", (event) => {
            reportKeyboard({
                keyPressed: event.key
            })
        });
    }

    function reportKeyboard(keyboardData){
        const payload = {
            keyPressed: keyboardData
        };
        collect(payload);
    }


    initKeyboardTracking();
    initMouseTracking();
    initErrorTracking();


    const activityLog = [];
    let lastActivity = Date.now();
    let idleStart = null;

    const idleThreshold = 30000;

    function activity(type) {
        const now = Date.now();

        // If they were idle, record the end of that idle period
        if (idleStart !== null) {
            activityLog.push({
                type: "idle_end",
                start: idleStart,
                end: now,
                duration: now - idleStart
            });

            reportIdle({
            idleEnd: now,
            idleDuration: now-idleStart
            })

            idleStart = null;
        }

        lastActivity = now;

        activityLog.push({
            type: type,
            timestamp: now
        });
    }

    document.addEventListener("mousemove", () => activity("mousemove"));
    document.addEventListener("keydown", () => activity("keydown"));
    document.addEventListener("click", () => activity("click"));
    window.addEventListener("scroll", () => activity("scroll"));

    setInterval(() => {
        const now = Date.now();

        if (idleStart === null && now - lastActivity >= idleThreshold) {
            idleStart = lastActivity;

            console.log("User became idle");
        }
    }, 1000);

    function reportIdle(idleData){
        collect(idleData);
    }
})();