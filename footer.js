fetch('footer.html')
  .then(response => response.text())
  .then(template => {
    const compiledTemplate = Handlebars.compile(template);
    const footerContainer = document.querySelector('.footer-container');
    footerContainer.innerHTML = compiledTemplate();
  });