import JudgeController from './JudgeController'
import SERC from './SERC'
import DigitalJudgeController from './DigitalJudgeController'
import DJJudgingController from './DJJudgingController'
import SpeedJudgingController from './SpeedJudgingController'
import DJDQController from './DJDQController'
import DJManageController from './DJManageController'

const DigitalJudge = {
    JudgeController: Object.assign(JudgeController, JudgeController),
    SERC: Object.assign(SERC, SERC),
    DigitalJudgeController: Object.assign(DigitalJudgeController, DigitalJudgeController),
    DJJudgingController: Object.assign(DJJudgingController, DJJudgingController),
    SpeedJudgingController: Object.assign(SpeedJudgingController, SpeedJudgingController),
    DJDQController: Object.assign(DJDQController, DJDQController),
    DJManageController: Object.assign(DJManageController, DJManageController),
}

export default DigitalJudge