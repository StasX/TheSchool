describe('Courses', () => {
    beforeEach(() => {
        cy.login(
            Cypress.env('OWNER_EMAIL'),
            Cypress.env('OWNER_PASSWORD')
        );

        cy.location('hash').should('eq', '#!school');
    });

    it('creates, edits and deletes a course', () => {
        const courseName = `Cypress Course ${Date.now()}`;
        const updatedCourseName = `${courseName} Updated`;

        // Create
        cy.get('#add-course').click();

        cy.get('#container-title')
            .should('contain.text', 'Add Course');

        cy.get('#name').type(courseName);

        cy.get('#description')
            .type('Course created by Cypress E2E test');

        cy.get('#image-file')
            .selectFile('cypress/fixtures/course.jpg');

        cy.get('#save-course').click();

        cy.get('#course-name')
            .should('contain.text', courseName);

        cy.get('#course-description')
            .should(
                'contain.text',
                'Course created by Cypress E2E test'
            );

        // Edit
        cy.get('#edit').click();

        cy.get('#container-title')
            .should('contain.text', 'Edit Course');

        cy.get('#name')
            .clear()
            .type(updatedCourseName);

        cy.get('#description')
            .clear()
            .type('Updated by Cypress');

        cy.get('#save-course').click();

        cy.get('#course-name')
            .should('contain.text', updatedCourseName);

        cy.get('#course-description')
            .should('contain.text', 'Updated by Cypress');

        // Delete
        cy.get('#edit').click();

        cy.contains('button', 'Delete')
            .click();

        cy.contains('button', 'Yes')
            .click();

        cy.contains('button', 'Continue')
            .click();

        cy.contains('Course deleted successfully!')
            .should('be.visible');

        cy.contains('button', 'OK')
            .click();

        cy.contains('.course-name', updatedCourseName)
            .should('not.exist');
    });
});