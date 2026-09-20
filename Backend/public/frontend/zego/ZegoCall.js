class ZegoCall {
    constructor({ appID, serverRegion, userID, userName, callType = 10 }) {
        this.appID = appID;
        this.serverRegion = serverRegion;
        this.userID = userID;
        this.userName = userName;
        this.callType = callType;

        this.zg = null;
        this.localStream = null;
        this.remoteStream = null;
        this.token = null;
        this.roomID = null;
    }

    async initZego() {
        this.zg = new ZegoExpressEngine(this.appID, this.serverRegion);
        this.zg.setLogConfig({ logLevel: 'info' });
    }

    async fetchToken(roomID) {
        this.roomID = roomID;
        const res = await fetch(`/api/generateZegoToken?user_id=${this.userID}&room_id=${roomID}`);
        const data = await res.json();
        this.token = data.token;
    }

    async joinRoom(roomID) {
        await this.fetchToken(roomID);
        await this.initZego();

        await this.zg.loginRoom(
            roomID,
            this.token,
            {
                userID: this.userID,
                userName: this.userName
            }
        );

        // Dynamic audio/video setting based on callType
        if(this.callType === 11){
            const enableVideo = true;
        }else{
            const enableVideo = false;
        }

        this.localStream = await this.zg.createStream({
            camera: {
                audio: true,
                video: enableVideo
            }
        });

        this.zg.startPublishingStream(`${this.userID}_stream`, this.localStream);

        return this.localStream;
    }

    onRemoteStream(callback) {
        this.zg.on('roomStreamUpdate', async (roomID, updateType, streamList) => {
            if (updateType === 'ADD' && streamList.length > 0) {
                this.remoteStream = await this.zg.startPlayingStream(streamList[0].streamID);
                callback(this.remoteStream);
            }

            if (updateType === 'DELETE') {
                this.remoteStream = null;
                callback(null);
            }
        });
    }

    async leaveRoom() {
        if (this.remoteStream) {
            this.zg.stopPlayingStream(this.remoteStream);
        }

        if (this.localStream) {
            this.zg.stopPublishingStream(`${this.userID}_stream`);
            this.zg.destroyStream(this.localStream);
        }

        await this.zg.logoutRoom(this.roomID);
    }
}
