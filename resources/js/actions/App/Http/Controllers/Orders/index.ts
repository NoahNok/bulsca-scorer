import HeatController from './HeatController'
import DrawController from './DrawController'

const Orders = {
    HeatController: Object.assign(HeatController, HeatController),
    DrawController: Object.assign(DrawController, DrawController),
}

export default Orders