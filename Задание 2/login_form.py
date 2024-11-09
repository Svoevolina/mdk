class LoginForm:
    def __init__(self):
        self.username = ""
        self.password = ""

    def set_credentials(self, username, password):
        self.username = username
        self.password = password

    def validate(self):
        if self.username == "admin" and self.password == "password":
            return True
        return False
