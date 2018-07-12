'use strict';

let CloudSprite = cc.Sprite.extend({

    screenWidth: 0.0,
    pixelsPerSecond: 0,
    xOffset: 0,

    ctor: function (spriteFrameName) {
        this._super(spriteFrameName);
    },

    SetSpeedAndWidth: function (pps, width, Xoffset) {
        this.screenWidth = width;
        this.pixelsPerSecond = pps;
        this.xOffset = Xoffset;
    },

    Stop: function () {
        this.stopAllActions();
    },

    ReachedDestination: function (sender) {
        // reset to right of screen
        sender.x = sender.xOffset + sender.screenWidth;
        sender.Start();
    },

    Start: function () {
        this.stopAllActions();

        let currentX = this.x;
        let distance = currentX - -(this.xOffset);
        let time = distance / this.pixelsPerSecond;
        let destination = cc.p(-this.xOffset, this.y);

        let actionMove = cc.moveTo(time, destination);
        let actionMoveDone = cc.callFunc(this.ReachedDestination, this);

        this.runAction(cc.sequence(actionMove, actionMoveDone));
    }
});