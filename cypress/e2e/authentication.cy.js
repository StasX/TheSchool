describe('Authentication', () => {
    beforeEach(() => {
        cy.visit('/');
    });

    it('displays the login form', () => {
        cy.get('#login').should('be.visible');

        cy.get('#user')
            .should('be.visible')
            .and('have.attr', 'type', 'text');

        cy.get('#password')
            .should('be.visible')
            .and('have.attr', 'type', 'password');

        cy.get('#login')
            .find('button[type="submit"]')
            .should('be.visible')
            .and('contain.text', 'Login');
    });

    it('rejects invalid credentials', () => {
        cy.get('#user').type('invalid@example.com');
        cy.get('#password').type('wrong-password');

        cy.get('#login').submit();

        cy.get('#alerts')
            .should('be.visible')
            .and('contain.text', 'Invalid username or password');

        cy.location('hash').should('eq', '');
    });

    it('logs in with valid credentials', () => {
        cy.get('#user').type(Cypress.env('OWNER_EMAIL'));
        cy.get('#password').type(Cypress.env('OWNER_PASSWORD'));

        cy.get('#login').submit();

        cy.location('hash').should('eq', '#!school');

        cy.get('#login').should('not.exist');
    });
});
