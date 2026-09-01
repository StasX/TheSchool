describe('Administrators', () => {
    beforeEach(() => {
        cy.login(
            Cypress.env('OWNER_EMAIL'),
            Cypress.env('OWNER_PASSWORD')
        );

        cy.visit('/#!administration');

        cy.location('hash')
            .should('eq', '#!administration');
    });

    it('creates, edits and deletes an administrator', () => {
        const email = `cypress-admin-${Date.now()}@example.com`;
        const updatedName = 'Updated Cypress Administrator';

        // Create
        cy.get('#add-administrator').click();

        cy.get('#container-title')
            .should('contain.text', 'Add Administrator');

        cy.get('#name')
            .type('Cypress Administrator');

        cy.get('#phone')
            .type('0501234567');

        cy.get('#email')
            .type(email);

        cy.get('#role')
            .select('manager');

        cy.get('#password')
            .type('CypressPassword123!');

        cy.get('#image-file')
            .selectFile('cypress/fixtures/administrator.jpg');

        cy.get('#save-administrator')
            .click();

        // After creation your handler immediately opens edit mode.
        cy.get('#container-title')
            .should('contain.text', 'Edit Administrator');

        cy.get('#name')
            .should('have.value', 'Cypress Administrator');

        cy.get('#email')
            .should('have.value', email);

        cy.get('#role')
            .should('have.value', 'manager');

        // Edit
        cy.get('#name')
            .clear()
            .type(updatedName);

        cy.get('#phone')
            .clear()
            .type('0507654321');

        cy.get('#role')
            .select('sales');

        // Password is optional during update.
        cy.get('#save-administrator')
            .click();

        cy.get('#name')
            .should('have.value', updatedName);

        cy.get('#phone')
            .should('have.value', '0507654321');

        cy.get('#role')
            .should('have.value', 'sales');

        // Make sure list was refreshed too.
        cy.contains('.administrator-name', updatedName)
            .should('exist');

        cy.contains('.administrator-role', 'sales')
            .should('exist');

        // Delete
        cy.contains('button', 'Delete')
            .click();

        cy.contains('button', 'Yes')
            .click();

        cy.contains('button', 'Continue')
            .click();

        cy.contains('Administrator deleted successfully!')
            .should('be.visible');

        cy.contains('button', 'OK')
            .click();

        cy.contains('.administrator-name', updatedName)
            .should('not.exist');
    });
});