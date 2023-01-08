fetch('navbar.html')
  .then(response => response.text())
  .then(template => {
    const compiledTemplate = Handlebars.compile(template);
    const navbarContainer = document.querySelector('.navbar-container');
    navbarContainer.innerHTML = compiledTemplate();
  });
