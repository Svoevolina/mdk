describe('Navigation Visibility Test for Goldapple', () => {
    const viewports = [
        { name: 'Desktop', width: 1280, height: 800 },
        { name: 'Tablet', width: 768, height: 1024 },
        { name: 'Mobile', width: 375, height: 667 }
    ];

    viewports.forEach(viewport => {
        it(`should display navigation correctly on ${viewport.name}`, () => {
            // Устанавливаем размер окна для текущего устройства
            cy.viewport(viewport.width, viewport.height);

            // Переход на главную страницу сайта
            cy.visit('https://goldapple.ru/');

            // Проверяем видимость навигационных элементов
            cy.get('nav').should('be.visible'); // Проверяем, что навигация видима

            // Проверяем наличие основных ссылок в навигации
            cy.get('nav').within(() => {
                cy.get('a').contains('О нас').should('be.visible');
                cy.get('a').contains('Контакты').should('be.visible');
                cy.get('a').contains('Каталог').should('be.visible');
                cy.get('a').contains('Акции').should('be.visible');
            });

            // Дополнительные проверки, если необходимо
            // Например, проверка наличия выпадающего меню или других элементов
        });
    });
});
