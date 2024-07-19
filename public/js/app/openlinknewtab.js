function openLinkNewTab(link) {
    window.open(link[0], '_blank', 'noopener,noreferrer');
}

document.addEventListener('open-link-new-tab-event', function (event) {
    openLinkNewTab(event.detail);
});

