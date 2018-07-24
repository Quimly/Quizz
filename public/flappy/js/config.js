let res = {};

res.BG_IMAGE = 'images/BG-HD.png';
res.FLOOR_IMAGE = 'images/Floor-HD.png';
res.ROBIN_IMAGE = 'images/Robin-HD.png';
res.TREE_IMAGE = 'images/Tree-HD.png';
res.CLOUD_IMAGE = 'images/Cloud-HD.png';
res.MOUNT_IMAGE = 'images/Mount-HD.png';
res.TUBE_IMAGE = 'images/Tube-HD.png';

let kZindexBG = 0;
let kZindexFloor = 40;
let kZindexRobin = 100;
let kZindexCloudSlow = 10;
let kZindexCloudFast = 20;
let kZindexTree = 50;
let kZindexMount = 30;
let kZindexTube = 35;

let kRobinStateStopped = 0;
let kRobinStateMoving = 1;
let kRobinStartSpeedY = 300;
let kRobinStartX = 240;

let kCloudRestartX = 100;
let kMountRestartX = 300;

let kCloudSpeedSlow = 13.0;
let kCloudSpeedFast = 53.0;
let kMountSpeed = 30.0;
let kTreeSpeed = 70.0;

let kCloudScaleSlow = 0.4;
let kCloudScaleFast = 0.85;
let kMountScale = 0.8;
let kTreeScale = 1.0;

let GRAVITY = -620;

let TRUE = 1;
let FALSE = 0;

let kReenableTime = 2.5;
let kTubeSpawnMinTime = 2.3;
let kTubeSpawnTimeVariance = 8;

let kSingleGapTop = 440;
let kSingleGapBottom = 230;
let kSingleGapMax = 280;
let kSingleGapMin = 160;

let kDoubleGapTop = 480;
let kDoubleGapBottom = 120;
let kDoubleGapMax = 220;
let kDoubleGapMin = 140;

let kTubeTypeUpper = 0;
let kTubeTypeLower = 1;
let kTubeTypePair = 2;
let kTubeTypeNone = 3;

let kTubeStateActive = 0;
let kTubeStateInActive = 1;

let kTubeOffsetX = 100;
let kTubeInactiveX = -1000;

let kTubeScore = 1;

let kTubeMaxUpPixels = 180;
let kFontSizeGameOver = 48;
let kFontSizeScore = 24;
let kFontName = 'Courier';
let kScoreX = 20;
let kScoreY = 20;

