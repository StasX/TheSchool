describe('Authorization', () => {

    it('allows owner to access administration', () => {
        cy.login(
            Cypress.env('OWNER_EMAIL'),
            Cypress.env('OWNER_PASSWORD')
        );

        cy.contains('a', 'Administration')
            .should('be.visible')
            .click();

        cy.location('hash')
            .should('eq', '#!administration');
    });

    it('allows manager to access administration', () => {
        cy.login(
            Cypress.env('MANAGER_EMAIL'),
            Cypress.env('MANAGER_PASSWORD')
        );

        cy.contains('a', 'Administration')
            .should('be.visible')
            .click();

        cy.location('hash')
            .should('eq', '#!administration');
    });

    it('hides administration from sales user', () => {
        cy.login(
            Cypress.env('SALES_EMAIL'),
            Cypress.env('SALES_PASSWORD')
        );

        cy.contains('a', 'Administration')
            .should('not.exist');

        cy.location('hash')
            .should('eq', '#!school');
    });

    it('redirects sales user from administration page', () => {
        cy.login(
            Cypress.env('SALES_EMAIL'),
            Cypress.env('SALES_PASSWORD')
        );

        cy.visit('/#!administration');

        cy.location('hash')
            .should('eq', '#!school');

        cy.contains('a', 'Administration')
            .should('not.exist');
    });

});