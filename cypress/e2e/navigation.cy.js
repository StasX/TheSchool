describe('Navigation', () => {
    beforeEach(() => {
        cy.login(
            Cypress.env('OWNER_EMAIL'),
            Cypress.env('OWNER_PASSWORD')
        );
    });

    it('opens the school page', () => {
        cy.visit('/#!school');

        cy.location('hash')
            .should('eq', '#!school');

        cy.get('#students-container')
            .should('be.visible');

        cy.get('#courses-container')
            .should('be.visible');
    });

    it('navigates from school to administration', () => {
        cy.contains('a', 'Administration')
            .click();

        cy.location('hash')
            .should('eq', '#!administration');

        cy.get('#administrators-container')
            .should('be.visible');
    });

    it('navigates from administration back to school', () => {
        cy.visit('/#!administration');

        cy.contains('a', 'School')
            .click();

        cy.location('hash')
            .should('eq', '#!school');

        cy.get('#students-container')
            .should('be.visible');

        cy.get('#courses-container')
            .should('be.visible');
    });

    it('displays not found page for an unknown route', () => {
        cy.visit('/#!unknown');

        cy.location('hash')
            .should('eq', '#!unknown');

        cy.contains('404')
            .should('be.visible');
        
            cy.contains('Page Not Found')
            .should('be.visible');
    });
});