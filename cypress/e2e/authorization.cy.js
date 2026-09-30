describe('Authorization', () => {

    it('allows owner to access administration', () => {
        cy.login(
            Cypress.expose('OWNER_EMAIL'),
            Cypress.expose('OWNER_PASSWORD')
        );

        cy.contains('a', 'Administration')
            .should('be.visible')
            .click();

        cy.location('hash')
            .should('eq', '#!administration');
    });

    it('allows manager to access administration', () => {
        cy.login(
            Cypress.expose('MANAGER_EMAIL'),
            Cypress.expose('MANAGER_PASSWORD')
        );

        cy.contains('a', 'Administration')
            .should('be.visible')
            .click();

        cy.location('hash')
            .should('eq', '#!administration');
    });

    it('hides administration from sales user', () => {
        cy.login(
            Cypress.expose('SALES_EMAIL'),
            Cypress.expose('SALES_PASSWORD')
        );

        cy.contains('a', 'Administration')
            .should('not.exist');

        cy.location('hash')
            .should('eq', '#!school');
    });

    it('redirects sales user from administration page', () => {
        cy.login(
            Cypress.expose('SALES_EMAIL'),
            Cypress.expose('SALES_PASSWORD')
        );

        cy.visit('/#!administration');

        cy.location('hash')
            .should('eq', '#!school');

        cy.contains('a', 'Administration')
            .should('not.exist');
    });

});