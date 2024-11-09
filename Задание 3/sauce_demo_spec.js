describe('Login and Authorization', () => {
    it('should fill the login form and authorize', () => {
        cy.visit('https://www.saucedemo.com/');

        // Заполнение формы логина
        cy.get('input[name="user-name"]').type('standard_user');
        cy.get('input[name="password"]').type('secret_sauce');
        
        // Отправка формы
        cy.get('input[type="submit"]').click();

        // Проверка, что пользователь успешно авторизован
        cy.url().should('include', '/inventory.html');
        cy.get('.title').should('have.text', 'Products');
    });
});

describe('Sorting Products by Price', () => {
    beforeEach(() => {
        cy.visit('https://www.saucedemo.com/');
        cy.get('input[name="user-name"]').type('standard_user');
        cy.get('input[name="password"]').type('secret_sauce');
        cy.get('input[type="submit"]').click();
    });

    it('should sort products by price in ascending order', () => {
        // Сортировка по цене (по возрастанию)
        cy.get('.product_sort_container').select('Price (low to high)');

        // Проверка, что цены отсортированы по возрастанию
        let prices = [];
        cy.get('.inventory_item_price').each(($el) => {
            prices.push(parseFloat($el.text().replace('$', '')));
        }).then(() => {
            const sortedPrices = [...prices].sort((a, b) => a - b);
            expect(prices).to.deep.equal(sortedPrices);
        });
    });

    it('should sort products by price in descending order', () => {
        // Сортировка по цене (по убыванию)
        cy.get('.product_sort_container').select('Price (high to low)');

        // Проверка, что цены отсортированы по убыванию
        let prices = [];
        cy.get('.inventory_item_price').each(($el) => {
            prices.push(parseFloat($el.text().replace('$', '')));
        }).then(() => {
            const sortedPrices = [...prices].sort((a, b) => b - a);
            expect(prices).to.deep.equal(sortedPrices);
        });
    });
});

describe('Add Products to Cart and Create Order', () => {
    beforeEach(() => {
        cy.visit('https://www.saucedemo.com/');
        cy.get('input[name="user-name"]').type('standard_user');
        cy.get('input[name="password"]').type('secret_sauce');
        cy.get('input[type="submit"]').click();
    });

    it('should add two products to the cart and create an order', () => {
        // Добавление первого товара
        cy.get('.inventory_item').first().find('button').click();
        
        // Добавление второго товара
        cy.get('.inventory_item').eq(1).find('button').click();
        
        // Переход в корзину
        cy.get('.shopping_cart_link').click();

        // Проверка, что в корзине два товара
        cy.get('.cart_item').should('have.length', 2);

        // Оформление заказа
        cy.get('.checkout').click();
        
        // Заполнение формы для оформления заказа
        cy.get('input[name="firstName"]').type('John');
        cy.get('input[name="lastName"]').type('Doe');
        cy.get('input[name="postalCode"]').type('12345');
        cy.get('.btn_primary.cart_button').click();

        // Проверка, что мы на странице оформления заказа
        cy.url().should('include', '/checkout-step-two.html');
        cy.get('.complete-header').should('have.text', 'THANK YOU FOR YOUR ORDER');
    });
});
