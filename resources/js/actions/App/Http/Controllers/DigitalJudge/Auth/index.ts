import LoginController from './LoginController'
import RegisterController from './RegisterController'
import ForgotPasswordController from './ForgotPasswordController'
import AccountController from './AccountController'

const Auth = {
    LoginController: Object.assign(LoginController, LoginController),
    RegisterController: Object.assign(RegisterController, RegisterController),
    ForgotPasswordController: Object.assign(ForgotPasswordController, ForgotPasswordController),
    AccountController: Object.assign(AccountController, AccountController),
}

export default Auth