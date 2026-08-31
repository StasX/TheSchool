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
            .and('contain', 'Login');
    });

    it('rejects invalid credentials', () => {
        cy.get('#user').type('invalid@example.com');
        cy.get('#password').type('wrong-password');

        cy.get('#login').submit();

        cy.get('#alerts')
            .should('be.visible')
            .and('contain', 'Invalid username or password');

        cy.location('hash').should('eq', '');
    });

});