function switchLanguage(language) {
    // Set the lang attribute on the html tag
    document.documentElement.lang = language;

    // Get all elements with a lang attribute
    const elements = document.querySelectorAll('[lang]');

    elements.forEach(element => {
        // Show elements that match the selected language, hide others
        if (element.getAttribute('lang') === language) {
            element.classList.remove('hidden');
        } else {
            element.classList.add('hidden');
        }
    });

    console.log("switchted to: ", document.documentElement.lang);
}
