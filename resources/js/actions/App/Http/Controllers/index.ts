import DigitalJudge from './DigitalJudge'
import PublicResultsController from './PublicResultsController'
import LiveController from './LiveController'
import WhatIf from './WhatIf'
import PublicStatsController from './PublicStatsController'
import ResultsToJSONController from './ResultsToJSONController'
import CompetitionController from './CompetitionController'
import LeagueController from './LeagueController'
import SpeedsEventController from './SpeedsEventController'
import SERCController from './SERCController'
import TeamsController from './TeamsController'
import EntityController from './EntityController'
import Result from './Result'
import OverallResultsController from './OverallResultsController'
import Orders from './Orders'
import PrintableController from './PrintableController'
import Push from './Push'
import Organisation from './Organisation'
import ChampionshipController from './ChampionshipController'
import AccountController from './AccountController'
import AdminController from './AdminController'
import Activity from './Activity'
import SERC from './SERC'
import AccountInviteController from './AccountInviteController'
import Landing from './Landing'
import Auth from './Auth'

const Controllers = {
    DigitalJudge: Object.assign(DigitalJudge, DigitalJudge),
    PublicResultsController: Object.assign(PublicResultsController, PublicResultsController),
    LiveController: Object.assign(LiveController, LiveController),
    WhatIf: Object.assign(WhatIf, WhatIf),
    PublicStatsController: Object.assign(PublicStatsController, PublicStatsController),
    ResultsToJSONController: Object.assign(ResultsToJSONController, ResultsToJSONController),
    CompetitionController: Object.assign(CompetitionController, CompetitionController),
    LeagueController: Object.assign(LeagueController, LeagueController),
    SpeedsEventController: Object.assign(SpeedsEventController, SpeedsEventController),
    SERCController: Object.assign(SERCController, SERCController),
    TeamsController: Object.assign(TeamsController, TeamsController),
    EntityController: Object.assign(EntityController, EntityController),
    Result: Object.assign(Result, Result),
    OverallResultsController: Object.assign(OverallResultsController, OverallResultsController),
    Orders: Object.assign(Orders, Orders),
    PrintableController: Object.assign(PrintableController, PrintableController),
    Push: Object.assign(Push, Push),
    Organisation: Object.assign(Organisation, Organisation),
    ChampionshipController: Object.assign(ChampionshipController, ChampionshipController),
    AccountController: Object.assign(AccountController, AccountController),
    AdminController: Object.assign(AdminController, AdminController),
    Activity: Object.assign(Activity, Activity),
    SERC: Object.assign(SERC, SERC),
    AccountInviteController: Object.assign(AccountInviteController, AccountInviteController),
    Landing: Object.assign(Landing, Landing),
    Auth: Object.assign(Auth, Auth),
}

export default Controllers