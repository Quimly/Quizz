'use strict';

let ArrayClouds = [];

let GameLayer = cc.Layer.extend({
    ctor:function () {
        this._super();
        this.init();
    },
    init:function () {
        this._super();
        let size = cc.director.getWinSize();

        let bgSprite = cc.Sprite.create(res.BG_IMAGE);
        bgSprite.setPosition(size.width / 2, size.height / 2);
        // sprite.setScale(0.75);
        this.addChild(bgSprite, kZindexBG);

        this._floor = cc.Sprite.create(res.FLOOR_IMAGE);
        this._floor.setPosition(0, 0);
        this._floor.setAnchorPoint(0, 0);
        this.addChild(this._floor, kZindexFloor);

        this._robin = new RobinSprite(res.ROBIN_IMAGE);
        this._robin.setPosition(kRobinStartX, size.height / 2);
        this._robin.topOfScreen = size.height;
        this._robin.Reset();
        this.addChild(this._robin, kZindexRobin);

        this.CreateClouds();
    },
    onEnter:function () {
        this._super();
        cc.eventManager.addListener({
            event: cc.EventListener.TOUCH_ONE_BY_ONE,
            swallowTouches: true,
            onTouchBegan: this.onTouchBegan,
            onTouchMoved: this.onTouchMoved,
            onTouchEnded: this.onTouchEnded
        }, this);

        this.schedule(this.onTick);
    },

    onTick: function (dt) {
        if (this._robin.y < this._floor.y / 2) {
            this._robin.Reset();
            this.StopClouds();
            this._robin.y = cc.director.getWinSize().height / 2;
        }
        this._robin.UpdateRobin(dt);
    },

    onTouchBegan: function (touch, event) {
        let tp = touch.getLocation();
        let target = event.getCurrentTarget();

        if (target._robin.state === kRobinStateStopped) {
            target._robin.state = kRobinStateMoving;
            target.StartClouds();
        }
        target._robin.SetStartSpeed();

        return false;
    },

    onTouchMoved: function (touch, event) {
        let tp = touch.getLocation();
        console.log('onTouchMoved: ' + tp.x.toFixed(2) + ', ' + tp.y.toFixed(2));
    },

    onTouchEnded: function (touch, event) {
        let tp = touch.getLocation();
        console.log('onTouchEnded: ' + tp.x.toFixed(2) + ', ' + tp.y.toFixed(2));
    },

    AddCloud: function (speed, position, scale, zIndex, name, XOffset) {
        let screenSize = cc.director.getWinSize();
        let cloud = new CloudSprite(name);
        cloud.SetSpeedAndWidth(speed, screenSize.width, XOffset);
        cloud.x = position.x;
        cloud.y = position.y;
        cloud.setScale(scale);
        this.addChild(cloud, zIndex);
        ArrayClouds[ArrayClouds.length] = cloud;
    },

    CreateClouds: function () {
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
    }
});

GameLayer.scene = function() {
    let scene = new cc.Scene();
    let layer = new GameLayer();
    scene.addChild(layer);
    return scene;
};

$(function () {

    let targetWidth = 960;
    let targetHeight = 640;

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
});