import { library, dom } from '@fortawesome/fontawesome-svg-core'
import {
    faDropbox, faGoogleDrive
} from '@fortawesome/free-brands-svg-icons'
import {
    faFile, faListAlt as faListAltRegular, faUser as faUserRegular
} from '@fortawesome/free-regular-svg-icons'
import {
    faAngleLeft, faAngleRight, faArrowUp, faArrowDown,
    faTrashAlt, faCloudUploadAlt, faUser, faLock,
    faDownload, faServer, faGlobe,
    faPlus, faCheck, faTimes,
    faEdit, faSignOutAlt, faSave, faExclamationCircle,
    faAsterisk, faBoxOpen, faDatabase, faTasks,
    faCloud, faCog, faArchive,
    faInfoCircle, faEye, faMinus, faUndo,
    faSearch, faPlay, faCaretDown,
    faCaretUp, faUsers, faEnvelope, faCopy,
    faExclamationTriangle, faCircle, faList, faListAlt,
    faTimesCircle, faCheckDouble, faSlidersH,
    faChartPie, faChartLine
} from '@fortawesome/free-solid-svg-icons'

library.add([
    faAngleLeft, faAngleRight, faArrowUp, faArrowDown,
    faTrashAlt, faCloudUploadAlt, faUser, faLock,
    faDownload, faDropbox, faServer, faGlobe,
    faGoogleDrive, faPlus, faCheck, faTimes,
    faEdit, faSignOutAlt, faSave, faExclamationCircle,
    faAsterisk, faBoxOpen, faDatabase, faTasks,
    faCloud, faCog, faArchive, faFile,
    faInfoCircle, faEye, faMinus, faUndo,
    faSearch, faPlay, faCaretDown,
    faCaretUp, faUsers, faEnvelope, faCopy,
    faExclamationTriangle, faCircle, faList, faListAlt,
    faTimesCircle, faCheckDouble, faSlidersH,
    faListAltRegular, faChartPie, faUserRegular,
    faChartLine
]);

dom.watch();