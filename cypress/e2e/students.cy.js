describe('Students', () => {
    beforeEach(() => {
        cy.login(
            Cypress.env('OWNER_EMAIL'),
            Cypress.env('OWNER_PASSWORD')
        );
    });

    it('creates, edits and deletes a student', () => {
        const email = `cypress-${Date.now()}@example.com`;

        // Create
        cy.get('#add-student').click();

        cy.get('#name').type('Cypress Student');
        cy.get('#phone').type('0501234567');
        cy.get('#email').type(email);

        cy.get('#image-file')
            .selectFile('cypress/fixtures/student.png');

        cy.get('#save-student').click();

        cy.get('#student-name')
            .should('contain.text', 'Cypress Student');

        cy.get('#student-email')
            .should('contain.text', email);

        // Edit
        cy.get('#edit').click();

        cy.get('#name')
            .clear()
            .type('Updated Cypress Student');

        cy.get('#phone')
            .clear()
            .type('0507654321');

        cy.get('#save-student').click();

        cy.get('#student-name')
            .should('contain.text', 'Updated Cypress Student');

        cy.get('#student-phone')
            .should('contain.text', '0507654321');

        // Edit again to expose Delete
        cy.get('#edit').click();

        // Delete
        cy.contains('button', 'Delete').click();

        cy.contains('button', 'Yes').click();
        cy.contains('button', 'Continue').click();

        cy.contains('Student deleted successfully!')
            .should('be.visible');

        cy.contains('button', 'OK').click();

        cy.contains('.student-name', 'Updated Cypress Student')
            .should('not.exist');
    });
});