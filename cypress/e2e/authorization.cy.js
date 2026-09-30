describe('Authorization', () => {
    it('allows owner to access administration', () => {
        cy.env(['OWNER_EMAIL', 'OWNER_PASSWORD'])
            .then(({ OWNER_EMAIL, OWNER_PASSWORD }) => {
                cy.login(OWNER_EMAIL, OWNER_PASSWORD);
            });

        cy.contains('a', 'Administration')
            .should('be.visible')
            .click();

        cy.location('hash')
            .should('eq', '#!administration');
    });

    it('allows manager to access administration', () => {
        cy.env(['MANAGER_EMAIL', 'MANAGER_PASSWORD'])
            .then(({ MANAGER_EMAIL, MANAGER_PASSWORD }) => {
                cy.login(MANAGER_EMAIL, MANAGER_PASSWORD);
            });

        cy.contains('a', 'Administration')
            .should('be.visible')
            .click();

        cy.location('hash')
            .should('eq', '#!administration');
    });

    it('hides administration from sales user', () => {
        cy.env(['SALES_EMAIL', 'SALES_PASSWORD'])
            .then(({ SALES_EMAIL, SALES_PASSWORD }) => {
                cy.login(SALES_EMAIL, SALES_PASSWORD);
            });

        cy.contains('a', 'Administration')
            .should('not.exist');

        cy.location('hash')
            .should('eq', '#!school');
    });

    it('redirects sales user from administration page', () => {
        cy.env(['SALES_EMAIL', 'SALES_PASSWORD'])
            .then(({ SALES_EMAIL, SALES_PASSWORD }) => {
                cy.login(SALES_EMAIL, SALES_PASSWORD);
            });

        cy.visit('/#!administration');

        cy.location('hash')
            .should('eq', '#!school');

        cy.contains('a', 'Administration')
            .should('not.exist');
    });
});
