import unittest
from login_form import LoginForm

class TestLoginForm(unittest.TestCase):

    def setUp(self):
        """Создание экземпляра формы перед каждым тестом."""
        self.form = LoginForm()

    def test_valid_credentials(self):
        """Тестирование валидных учетных данных."""
        self.form.set_credentials("admin", "password")
        self.assertTrue(self.form.validate())

    def test_invalid_username(self):
        """Тестирование невалидного имени пользователя."""
        self.form.set_credentials("user", "password")
        self.assertFalse(self.form.validate())

    def test_invalid_password(self):
        """Тестирование невалидного пароля."""
        self.form.set_credentials("admin", "wrongpassword")
        self.assertFalse(self.form.validate())

    def test_empty_credentials(self):
        """Тестирование пустых учетных данных."""
        self.form.set_credentials("", "")
        self.assertFalse(self.form.validate())

if __name__ == '__main__':
    unittest.main()
