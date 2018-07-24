let ArrayClouds = [];
let ArrayTubes = [];

let GameLayer = cc.Layer.extend({
    ctor:function() {
        this._super();
        this.init();
    },
    init:function() {
        this._super();
        let size = cc.director.getWinSize();

        let bgsprite = cc.Sprite.create(res.BG_IMAGE);
        bgsprite.setPosition(size.width / 2, size.height / 2);
        this.addChild(bgsprite, kZindexBG);

        this._floor = cc.Sprite.create(res.FLOOR_IMAGE);
        this._floor.setPosition(0, 0);
        this._floor.setAnchorPoint(0,0);
        this.addChild(this._floor, kZindexFloor);

        this._robin = new RobinSprite(res.ROBIN_IMAGE);
        this._robin.x = kRobinStartX;
        this._robin.y = size.height / 2;
        this._robin.topOfScreen = size.height;
        this._robin.Reset();
        this.addChild(this._robin, kZindexRobin);

        this.CreateClouds();

        this._gameTime = 0;
        this._gameStarted = FALSE;
        this._middleY = size.height / 2;
        this._processTouch = FALSE;

        this._lastSpawnTime = 0;
        this._nextSpawnTime = 0;

        this._lastTubeType = kTubeTypeNone;
        this._lastGetUnderY = 0;

        this._score = 0;
        this._highScore = 0;

        this._gameOverLabel = this.addLabel("Game Over!",size.width / 2, size.height / 2, FALSE, kZindexRobin,
            cc.color.RED, kFontSizeGameOver );
        this._gameStartLabel = this.addLabel("Cliquez Pour commencer", size.width / 2, size.height /  3 * 2, TRUE, kZindexRobin,
            cc.color.RED, kFontSizeGameOver );
        this._scoreLabel = this.addLabel("00000", kScoreX, size.height - kScoreY, TRUE, kZindexRobin,
            cc.color.RED, kFontSizeScore );
        this._highScoreLabel = this.addLabel("10000", kScoreX, size.height - kScoreY * 3, TRUE, kZindexRobin,
            cc.color.RED, kFontSizeScore );
        this._scoreLabel.setAnchorPoint(0,1);
        this._highScoreLabel.setAnchorPoint(0,1);

        this.setScoreLabels();
    },

    setScoreLabels: function() {
        this._scoreLabel.string = this._score.toString();
        this._highScoreLabel.string = this._highScore.toString();
    },

    addLabel: function(text, x, y, vis, zin, col, fsize) {
        var label = new cc.LabelTTF(text, kFontName, fsize);
        label.setPosition(x, y);
        label.color = col;
        label.visible = vis;
        this.addChild(label, zin);
        return label;
    },
    
    onEnter:function() {
        this._super();
        cc.eventManager.addListener({
            event: cc.EventListener.TOUCH_ONE_BY_ONE,
            swallowTouches: true,
            onTouchBegan: this.onTouchBegan,
            onTouchMoved: this.onTouchMoved,
            onTouchEnded: this.onTouchEnded
        }, this);

        this.schedule(this.onTick);

        this.StopGame();
        this._processTouch = TRUE;

    },

    onTick:function(dt) {

        let gameOver = FALSE;

        if(this._gameStarted == TRUE) {
            this._gameTime += dt;
            this._lastSpawnTime += dt;

            if(this._lastSpawnTime > this._nextSpawnTime) {
                console.log('onTick() Spawn Tubes');
                this.SetSpawnTime();
                this.SpawnNewTubes();
            }

            if(this._robin.y < this._floor.y / 2) {
                gameOver = TRUE;
            } else {
                let RobinCollBox = this._robin.TubeCollisionBox();
                for (let i = 0, len = ArrayTubes.length; i < len; i++) {
                    if (ArrayTubes[i].state ==  kTubeStateActive) {
                        if (cc.rectIntersectsRect(ArrayTubes[i].getBoundingBox(), RobinCollBox) == TRUE) {
                            console.log('game over');
                            gameOver = TRUE;
                        } else {
                            if (ArrayTubes[i].scored == FALSE) {
                                if (ArrayTubes[i].getBoundingBox().x + ArrayTubes[i].getBoundingBox().width <
                                this._robin.getBoundingBox().x) {
                                    ArrayTubes[i].scored = TRUE;
                                    this._score += kTubeScore;
                                    this.setScoreLabels();
                                }
                            }
                        }
                    }
                }
            }

            if(gameOver == FALSE) {
                this._robin.UpdateRobin(dt);
            } else {
                this.GameOver();
            }
        }
    },

    onTouchBegan:function(touch, event) {
        let tp = touch.getLocation();
        let tar = event.getCurrentTarget();

        if(tar._processTouch == TRUE) {
            tar._robin.SetStartSpeed();
            if(tar._gameStarted == FALSE) {
                tar.StartGame();
            }
        }

        return false;
    },

    onTouchMoved:function(touch, event) {
        let tp = touch.getLocation();
        console.log('onTouchMoved:' + tp.x.toFixed(2) + ','  + tp.y.toFixed(2));
    },

    onTouchEnded:function(touch, event) {
        let tp = touch.getLocation();
        console.log('onTouchEnded:' + tp.x.toFixed(2) + ','  + tp.y.toFixed(2));
    },

    AddCloud:function(speed, position, scale, zIndex, name, XOffset) {
        let screenSize = cc.director.getWinSize();
        let cloud = new CloudSprite(name);
        cloud.SetSpeedAndWidth(speed, screenSize.width, XOffset);
        cloud.x = position.x;
        cloud.y = position.y;
        cloud.setScale(scale);
        this.addChild(cloud, zIndex);
        ArrayClouds[ArrayClouds.length] = cloud;
    },

    CreateClouds:function() {
        let FileName = res.CLOUD_IMAGE;

        this.AddCloud(kCloudSpeedSlow, cc.p(700,610), kCloudScaleSlow, kZindexCloudSlow, FileName, kCloudRestartX);
        this.AddCloud(kCloudSpeedSlow, cc.p(150,570), kCloudScaleSlow, kZindexCloudSlow, FileName, kCloudRestartX);

        this.AddCloud(kCloudSpeedFast, cc.p(150,300), kCloudScaleFast, kZindexCloudFast, FileName, kCloudRestartX);
        this.AddCloud(kCloudSpeedFast, cc.p(400,500), kCloudScaleFast, kZindexCloudFast, FileName, kCloudRestartX);
        this.AddCloud(kCloudSpeedFast, cc.p(880,400), kCloudScaleFast, kZindexCloudFast, FileName, kCloudRestartX);

        FileName = res.MOUNT_IMAGE;
        this.AddCloud(kMountSpeed, cc.p(300,170), kMountScale, kZindexMount, FileName, kMountRestartX);
        this.AddCloud(kMountSpeed, cc.p(800,170), kMountScale, kZindexMount, FileName, kMountRestartX);

        FileName = res.TREE_IMAGE;
        this.AddCloud(kTreeSpeed, cc.p(128,72), kTreeScale, kZindexTree, FileName, kCloudRestartX);
        this.AddCloud(kTreeSpeed, cc.p(624,72), kTreeScale, kZindexTree, FileName, kCloudRestartX);
        this.AddCloud(kTreeSpeed, cc.p(864,72), kTreeScale, kZindexTree, FileName, kCloudRestartX);
    },

    StartClouds: function() {
        for (let i = 0,  len = ArrayClouds.length; i < len; ++i) {
            ArrayClouds[i].Start();
        }
    },

    StopClouds: function() {
        for (let i = 0,  len = ArrayClouds.length; i < len; ++i) {
            ArrayClouds[i].Stop();
        }
    },

    StopTubes:function() {
        for (let i = 0, len = ArrayTubes.length; i < len; i++) {
            ArrayTubes[i].stopAllActions();
        }
    },

    ClearTubes:function() {
        for (let i = 0, len = ArrayTubes.length; i < len; i++) {
            ArrayTubes[i].Stop();
        }
    },

    StartGame: function() {
        this._robin.state = kRobinStateMoving;
        this.StartClouds();
        this._gameStarted = TRUE;
        this._lastTubeType = kTubeTypeNone;
        this._lastGetUnderY = this._middleY;
        this._gameStartLabel.visible = FALSE;
    },

    StopGame: function() {
        this.StopClouds();
        this._gameStarted = FALSE;
        this._gameTime = 0;
        this._nextSpawnTime = 0.2;
        this.StopTubes();
    },

    GameOver: function() {
        this._processTouch = FALSE;
        this._gameOverLabel.visible = TRUE;
        this.StopGame();
        this.scheduleOnce(this.ReEnableAfterGameOver, kReenableTime);
    },

    ReEnableAfterGameOver: function() {
        this._robin.y = this._middleY;
        this._processTouch = TRUE;
        this._gameOverLabel.visible = FALSE;
        this._gameStartLabel.visible = TRUE;
        this.ClearTubes();

        if ( this._score >  this._highScore) {
            this._highScore = this._score;
        }

        this._score = 0;
        this.setScoreLabels();
    },

    SetSpawnTime: function() {
        this._lastSpawnTime = 0;
        this._nextSpawnTime = Math.floor( ( Math.random() *
            kTubeSpawnTimeVariance ) + 1  ) / 10 + kTubeSpawnMinTime;
        console.log('_nextSpawnTime set to:' , this._nextSpawnTime);
    },

    SpawnNewTubes: function() {
        let ourChance = Math.floor((Math.random() * 3) + 1);

        while(1) {
            if(this._lastTubeType == kTubeTypeUpper && ourChance == 1) {
                ourChance = Math.floor((Math.random() * 3) + 1);
            } else if(this._lastTubeType == kTubeTypeLower && ourChance == 2) {
                ourChance = Math.floor((Math.random() * 3) + 1);
            } else if(this._lastTubeType == kTubeTypePair && ourChance == 3) {
                ourChance = Math.floor((Math.random() * 3) + 1);
            } else {
                break;
            }
        }

        if(ourChance == 1) {
            this.SpawnUpperOrLower(TRUE);
        } else if(ourChance == 2) {
            this.SpawnUpperOrLower(FALSE);
        } else {
            this.SpawnTubePair();
        }
    },

    SpawnUpperOrLower: function(isUpper) {
        let YMax, YMin;
        if(isUpper == TRUE ) {
            this._lastTubeType = kTubeTypeUpper;
            YMax = this._middleY;
            YMin = kSingleGapBottom;
        } else {
            this._lastTubeType = kTubeTypeLower;
            YMax = kSingleGapTop;
            YMin = this._middleY;
            if(YMax - this._lastGetUnderY > kTubeMaxUpPixels) {
                YMax = this._lastGetUnderY + kTubeMaxUpPixels;
            }
        }

        let YRange = Math.abs(YMax - YMin);
        let YPos = YMax - Math.floor(Math.random() * (YRange));

        if(isUpper == TRUE) {
            this._lastGetUnderY = YPos;
        } else {
            this._lastGetUnderY = this._middleY;
        }
        console.log('SpawnUpperOrLower tube isUpper:' , isUpper , ' YPos:' , YPos);
        this.SpawnATube(isUpper, YPos);
    },

    SpawnTubePair: function() {
        this._lastTubeType = kTubeTypePair;
        let Gap = kDoubleGapMin + Math.floor(Math.random() * (kDoubleGapMax - kDoubleGapMin));
        let YRange = kDoubleGapTop - Gap - kDoubleGapBottom;
        let TopY = kDoubleGapTop - Math.floor(Math.random() * YRange);
        let BottomY = TopY - Gap;

        this._lastGetUnderY = TopY;

        console.log('SpawnTubePair TopY:' , TopY , ' BottomY:' , BottomY);
        this.SpawnATube(TRUE, TopY);
        this.SpawnATube(FALSE, BottomY);
    },

    SpawnATube:function(isUpper, yPos) {
        let tube = this.GetNextTube();

        if(isUpper == TRUE) {
            tube.setAnchorPoint(0.5,0);
            tube.setFlippedY(FALSE);
        } else {
            tube.setAnchorPoint(0.5,1);
            tube.setFlippedY(TRUE);
        }

        tube.y = yPos;
        tube.Start();
    },

    GetNextTube:function() {
        for (var i = 0, len = ArrayTubes.length; i < len; i++) {
            if(ArrayTubes[i].state == kTubeStateInActive) {
                console.log('found resuable tube');
                return ArrayTubes[i];
            }
        }

        let size = cc.director.getWinSize();

        let newTube = new TubeSprite(res.TUBE_IMAGE);
        newTube.Initialise(kTreeSpeed, size.width, kTubeOffsetX, kTubeInactiveX);
        this.addChild(newTube, kZindexTube);
        ArrayTubes[ArrayTubes.length] = newTube;
        console.log('made tube num:' + ArrayTubes.length);
        return newTube;
    },


});







GameLayer.scene = function() {
    var scene = new cc.Scene();
    var layer = new GameLayer();
    scene.addChild(layer);
    return scene;
};

window.onload = function(){

    var targetWidth = 960;
    var targetHeight = 640;

    cc.game.onStart = function(){

        cc.view.adjustViewPort(false);
        cc.view.setDesignResolutionSize(targetWidth, targetHeight, cc.ResolutionPolicy.SHOW_ALL);
        cc.view.resizeWithBrowserSize(true);
        //load resources
        cc.LoaderScene.preload(["images/HelloWorld.png"], function () {
            cc.director.runScene(GameLayer.scene());
        }, this);
    };
    cc.game.run("gameCanvas");
};
