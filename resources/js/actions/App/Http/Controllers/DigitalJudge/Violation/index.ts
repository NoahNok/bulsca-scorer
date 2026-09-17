import ViolationController from './ViolationController'
import ViolationStateController from './ViolationStateController'

const Violation = {
    ViolationController: Object.assign(ViolationController, ViolationController),
    ViolationStateController: Object.assign(ViolationStateController, ViolationStateController),
}

export default Violation