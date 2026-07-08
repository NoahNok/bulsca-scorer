import Sanctum from './Sanctum'
import Telescope from './Telescope'

const Laravel = {
    Sanctum: Object.assign(Sanctum, Sanctum),
    Telescope: Object.assign(Telescope, Telescope),
}

export default Laravel