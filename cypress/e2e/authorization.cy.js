describe('Authorization', () => {
    cy.env(['OWNER_EMAIL', 'OWNER_PASSWORD'])
        .then(({ OWNER_EMAIL, OWNER_PASSWORD }) => {
            it('allows owner to access administration', () => {
                cy.login(OWNER_EMAIL, OWNER_PASSWORD);

                cy.contains('a', 'Administration')
                    .should('be.visible')
                    .click();

                cy.location('hash')
                    .should('eq', '#!administration');
            });
        });


    cy.env(['MANAGER_EMAIL', 'MANAGER_PASSWORD'])
        .then(({ MANAGER_EMAIL, MANAGER_PASSWORD }) => {
            it('allows manager to access administration', () => {
                cy.login(MANAGER_EMAIL, MANAGER_PASSWORD);

                cy.contains('a', 'Administration')
                    .should('be.visible')
                    .click();

                cy.location('hash')
                    .should('eq', '#!administration');
            });
        });


    cy.env(['SALES_EMAIL', 'SALES_PASSWORD'])
        .then(({ SALES_EMAIL, SALES_PASSWORD }) => {
            it('hides administration from sales user', () => {
                cy.login(SALES_EMAIL, SALES_PASSWORD);

                cy.contains('a', 'Administration')
                    .should('not.exist');

                cy.location('hash')
                    .should('eq', '#!school');
            });
        });

    cy.env(['SALES_EMAIL', 'SALES_PASSWORD'])
        .then(({ SALES_EMAIL, SALES_PASSWORD }) => {
            it('redirects sales user from administration page', () => {
                cy.login(SALES_EMAIL, SALES_PASSWORD);

                cy.visit('/#!administration');

                cy.location('hash')
                    .should('eq', '#!school');

                cy.contains('a', 'Administration')
                    .should('not.exist');
            });
        });
});