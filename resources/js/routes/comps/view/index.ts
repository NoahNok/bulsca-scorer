import events from './events'
import speeds from './speeds'
import sercs from './sercs'
import teams from './teams'

const view = {
    events: Object.assign(events, events),
    speeds: Object.assign(speeds, speeds),
    sercs: Object.assign(sercs, sercs),
    teams: Object.assign(teams, teams),
}

export default view