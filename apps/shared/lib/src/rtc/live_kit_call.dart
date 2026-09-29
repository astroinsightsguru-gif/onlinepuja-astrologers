import 'dart:async';

import 'package:flutter/foundation.dart';
import 'package:livekit_client/livekit_client.dart' as lk;

import '../env.dart';

/// Reusable LiveKit (self-hosted, free) call session controller.
///
/// Used by both the customer app and the partner app. Wraps [lk.Room]:
/// connects, publishes local audio (and camera for video calls), tracks the
/// remote participant + video track, and exposes simple mic/camera toggles.
///
/// The room name is the consultation session id so customer and astrologer
/// land in the same room.
class LiveKitCallController extends ChangeNotifier {
  lk.Room? _room;
  lk.EventsListener<lk.RoomEvent>? _listener;
  lk.VideoTrack? _remoteVideoTrack;
  String? _remoteName;
  bool _disposed = false;

  bool _connecting = false;
  bool _connected = false;
  bool _micMuted = false;
  bool _camEnabled = false;

  /// Notify only while the controller is alive (async callbacks may fire
  /// after dispose()).
  void _safeNotify() {
    if (!_disposed) notifyListeners();
  }

  bool get connecting => _connecting;
  bool get connected => _connected;
  bool get micMuted => _micMuted;
  bool get camEnabled => _camEnabled;

  /// Remote peer's video track (null on pure audio calls / before subscribe).
  lk.VideoTrack? get remoteVideoTrack => _remoteVideoTrack;

  /// Local participant's video track (null when camera is off).
  lk.VideoTrack? get localVideoTrack {
    final pubs = _room?.localParticipant?.videoTrackPublications;
    if (pubs == null || pubs.isEmpty) return null;
    for (final pub in pubs) {
      if (pub.track != null && !pub.muted) return pub.track;
    }
    return null;
  }

  /// Remote peer's display name, once they join the room.
  String? get remoteName => _remoteName;

  /// Connect to the LiveKit room and publish local tracks.
  ///
  /// [wsUrl] defaults to [Env.liveKitUrl]; prefer the `wsUrl` returned by the
  /// backend token endpoint when present.
  Future<void> connect({
    required String token,
    String? wsUrl,
    required bool video,
    String? identity,
  }) async {
    if (_room != null) return;
    _connecting = true;
    _safeNotify();
    try {
      final room = lk.Room(
        roomOptions: lk.RoomOptions(
          adaptiveStream: true,
          dynacast: true,
        ),
      );
      _listener = room.createListener()
        ..on<lk.RoomConnectedEvent>((_) {
          _connected = true;
          _connecting = false;
          _refreshRemote(room);
          _safeNotify();
        })
        ..on<lk.ParticipantConnectedEvent>((e) {
          _refreshRemote(room);
          _safeNotify();
        })
        ..on<lk.ParticipantDisconnectedEvent>((e) {
          _refreshRemote(room);
          _safeNotify();
        })
        ..on<lk.TrackSubscribedEvent>((e) {
          final track = e.track;
          if (track is lk.VideoTrack) {
            _remoteVideoTrack = track;
          }
          _refreshRemote(room);
          _safeNotify();
        })
        ..on<lk.TrackUnsubscribedEvent>((e) {
          if (e.track == _remoteVideoTrack) {
            _remoteVideoTrack = null;
            _safeNotify();
          }
        })
        ..on<lk.RoomDisconnectedEvent>((_) {
          _connected = false;
          _connecting = false;
          _safeNotify();
        });

      await room.connect(
        wsUrl ?? Env.liveKitUrl,
        token,
        fastConnectOptions: lk.FastConnectOptions(
          microphone: const lk.TrackOption(enabled: true),
          camera: lk.TrackOption(enabled: video),
        ),
      );

      _room = room;
      _connected = true;
      _connecting = false;
      _camEnabled = video;
      await room.localParticipant?.setMicrophoneEnabled(true);
      _refreshRemote(room);
      _safeNotify();
    } catch (_) {
      _connecting = false;
      _connected = false;
      rethrow;
    } finally {
      _safeNotify();
    }
  }

  void _refreshRemote(lk.Room room) {
    final others = room.remoteParticipants.values.toList();
    if (others.isEmpty) {
      _remoteName = null;
      return;
    }
    final remote = others.first;
    _remoteName = remote.name.isNotEmpty ? remote.name : remote.identity;
  }

  /// Toggle the local microphone; optimistic UI, reverted on failure.
  Future<void> toggleMic() async {
    final room = _room;
    if (room == null) return;
    _micMuted = !_micMuted;
    _safeNotify();
    try {
      await room.localParticipant?.setMicrophoneEnabled(!_micMuted);
    } catch (_) {
      _micMuted = !_micMuted;
      _safeNotify();
    }
  }

  /// Toggle the local camera (video calls only).
  Future<void> toggleCam() async {
    final room = _room;
    if (room == null) return;
    final target = !_camEnabled;
    _safeNotify();
    try {
      await room.localParticipant?.setCameraEnabled(target);
      _camEnabled = target;
    } catch (_) {
      // keep previous state
    }
    _safeNotify();
  }

  lk.CameraPosition _cameraPosition = lk.CameraPosition.front;
  lk.CameraPosition get cameraPosition => _cameraPosition;

  /// Flip front / back camera on mobile devices.
  Future<void> switchCamera() async {
    final pubs = _room?.localParticipant?.videoTrackPublications;
    if (pubs == null || pubs.isEmpty) return;
    _cameraPosition = _cameraPosition == lk.CameraPosition.front
        ? lk.CameraPosition.back
        : lk.CameraPosition.front;
    for (final pub in pubs) {
      final track = pub.track;
      if (track is lk.LocalVideoTrack) {
        try {
          await track.restartTrack(
            lk.CameraCaptureOptions(cameraPosition: _cameraPosition),
          );
          _safeNotify();
        } catch (_) {}
      }
    }
  }

  /// Leave the room and release all resources.
  Future<void> leave() async {
    await _listener?.dispose();
    _listener = null;
    final room = _room;
    _room = null;
    _connected = false;
    _remoteVideoTrack = null;
    _remoteName = null;
    if (room != null) {
      try {
        await room.disconnect();
      } catch (_) {/* already gone */}
      await room.dispose();
    }
    _safeNotify();
  }

  @override
  void dispose() {
    _disposed = true;
    leave();
    super.dispose();
  }
}
