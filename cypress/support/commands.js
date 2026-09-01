Cypress.Commands.add('login', (email, password) => {
    cy.visit('/');

    cy.get('#user').type(email);
    cy.get('#password').type(password);

    cy.get('#login').submit();

    cy.location('hash')
        .should('eq', '#!school');
});