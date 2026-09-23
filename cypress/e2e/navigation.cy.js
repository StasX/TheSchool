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

    it('keeps an unsaved course form when row navigation is cancelled', () => {
        const name = `Navigation Student ${Date.now()}`;

        cy.get('#add-student').click();
        cy.get('#name').type(name);
        cy.get('#email').type(`navigation-${Date.now()}@example.com`);
        cy.get('#phone').type('0501234567');
        cy.get('#image-file').selectFile('cypress/fixtures/student.png');
        cy.get('#save-student').click();

        cy.get('#student-name').should('have.text', name);

        cy.get('#add-course').click();
        cy.get('#name').type('Unsaved course');

        cy.contains('#students-container .item-row', name).click();

        cy.contains('.swal2-popup', 'all changes will be discarded')
            .should('be.visible');

        cy.contains('.swal2-popup button', 'Cancel').click();

        cy.get('#courses-form').should('exist');
        cy.get('#name').should('have.value', 'Unsaved course');
    });
});