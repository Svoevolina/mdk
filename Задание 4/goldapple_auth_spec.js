describe('Authorization Form Test', () => {
    beforeEach(() => {
        // Переход на страницу авторизации
        cy.visit('https://goldapple.ru/');
        
        // Открытие формы авторизации (предполагается, что она доступна через кнопку "Войти")
        cy.get('.header__auth').click(); // Убедитесь, что селектор правильный
    });

    it('should successfully log in with valid credentials', () => {
        // Заполнение формы логина
        cy.get('input[name="email"]').type('your_email@example.com'); // Замените на ваш email
        cy.get('input[name="password"]').type('your_password'); // Замените на ваш пароль
        
        // Отправка формы
        cy.get('button[type="submit"]').click(); // Убедитесь, что селектор правильный

        // Проверка, что пользователь успешно авторизован
        cy.url().should('not.include', '/login'); // Предполагается, что после авторизации URL изменится
        cy.get('.user-profile').should('be.visible'); // Убедитесь, что селектор правильный
    });

    it('should show an error message with invalid credentials', () => {
        // Заполнение формы логина с неправильными данными
        cy.get('input[name="email"]').type('wrong_email@example.com'); // Неверный email
        cy.get('input[name="password"]').type('wrong_password'); // Неверный пароль
        
        // Отправка формы
        cy.get('button[type="submit"]').click(); // Убедитесь, что селектор правильный

        // Проверка, что отображается сообщение об ошибке
        cy.get('.error-message').should('be.visible'); // Убедитесь, что селектор правильный
        cy.get('.error-message').should('contain', 'Неверный логин или пароль'); // Замените текст на актуальный
    });
});
