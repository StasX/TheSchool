describe('Administrators', () => {
    beforeEach(() => {
        cy.login(Cypress.env('OWNER_EMAIL'), Cypress.env('OWNER_PASSWORD'));
        cy.visit('/#!administration');
        cy.location('hash').should('eq', '#!administration');
        cy.get('#administrators-container .item-row').should('have.length.greaterThan', 0);
    });

    it('creates, edits and deletes a non-owner administrator', () => {
        const suffix = Date.now();
        const email = `cypress-admin-${suffix}@example.com`;
        const name = `Cypress Admin ${suffix}`;
        const updatedName = `Updated Admin ${suffix}`;

        cy.get('#add-administrator').click();
        cy.get('#container-title').should('contain.text', 'Add Administrator');
        cy.get('#role option').then($options => {
            expect([...$options].map(option => option.value)).to.deep.equal(['manager', 'sales']);
        });
        cy.get('#name').type(name);
        cy.get('#phone').type('0501234567');
        cy.get('#email').type(email);
        cy.get('#role').select('manager');
        cy.get('#password').type('CypressPassword123!');
        cy.get('#image-file').selectFile('cypress/fixtures/administrator.png');
        cy.get('#save-administrator').click();

        cy.get('#container-title').should('contain.text', 'Edit Administrator');
        cy.get('#name').should('have.value', name);
        cy.get('#email').should('have.value', email);
        cy.get('#role').should('have.value', 'manager');
        cy.contains('#administrators-container .item-row', name).should('exist');

        cy.get('#name').clear().type(updatedName);
        cy.get('#phone').clear().type('0507654321');
        cy.get('#role').select('sales');
        cy.get('#password').should('not.have.attr', 'required');
        cy.get('#save-administrator').click();

        cy.get('#name').should('have.value', updatedName);
        cy.get('#phone').should('have.value', '0507654321');
        cy.get('#role').should('have.value', 'sales');
        cy.contains('#administrators-container .item-row', updatedName)
            .should('contain.text', 'sales');

        cy.contains('button', 'Delete').click();
        cy.contains('.swal2-popup button', 'Yes').click();
        cy.contains('.swal2-popup button', 'Continue').click();
        cy.contains('.swal2-popup', 'Administrator deleted successfully!')
            .should('be.visible');
        cy.contains('.swal2-popup button', 'OK').click();
        cy.contains('#administrators-container .item-row', updatedName).should('not.exist');
    });

    it('requires the fields needed to create an administrator', () => {
        cy.get('#add-administrator').click();
        cy.get('#save-administrator').click();
        cy.get('#administrators-form').should('exist');
        cy.get('#name').then($input => {
            expect($input[0].validity.valueMissing).to.equal(true);
        });
        cy.get('#password').should('have.attr', 'required');
        cy.get('#image-file').should('have.attr', 'required');
    });

    it('keeps changes on cancel and clears row navigation warnings on confirm', () => {
        cy.get('#add-administrator').click();
        cy.get('#name').type('Unsaved administrator');

        cy.get('#administrators-container .item-row').first().click();
        cy.contains('.swal2-popup', 'all changes will be discarded').should('be.visible');
        cy.contains('.swal2-popup button', 'Cancel').click();
        cy.get('#name').should('have.value', 'Unsaved administrator');

        cy.get('#administrators-container .item-row').first().click();
        cy.contains('.swal2-popup button', 'Continue').click();
        cy.get('#container-title').should('contain.text', 'Edit Administrator');

        cy.get('#add-administrator').click();
        cy.get('.swal2-popup').should('not.exist');
        cy.get('#container-title').should('contain.text', 'Add Administrator');
    });

    it('keeps changes on cancel and clears add warnings on confirm', () => {
        cy.get('#add-administrator').click();
        cy.get('#name').type('Unsaved administrator');

        cy.get('#add-administrator').click();
        cy.contains('.swal2-popup button', 'Cancel').click();
        cy.get('#name').should('have.value', 'Unsaved administrator');

        cy.get('#add-administrator').click();
        cy.contains('.swal2-popup button', 'Continue').click();
        cy.get('#container-title').should('contain.text', 'Add Administrator');
        cy.get('#name').should('have.value', '');
        cy.get('#add-administrator').click();
        cy.get('.swal2-popup').should('not.exist');
    });
});
