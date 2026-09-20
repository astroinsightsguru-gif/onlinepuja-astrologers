import 'dart:async';

import 'package:flutter/material.dart';
import 'package:livekit_client/livekit_client.dart' as lk;
import 'package:op_shared/op_shared.dart';
import 'package:provider/provider.dart';

import '../../../state/app_session.dart';

/// Audio/video call session screen.
///
/// Uses a single **LiveKit** path (free self-hosted WebRTC, docs 04 §4.2)
/// instead of the legacy Agora/Zego/HMS triple stack. The backend mints a
/// LiveKit token via `POST /api/livekit/token`. If the endpoint is not yet
/// deployed this screen degrades gracefully to a REST session with a live
/// billing timer, so the flow stays testable end-to-end.
class AudioCallScreen extends StatefulWidget {
  const AudioCallScreen({
    super.key,
    required this.astrologerId,
    required this.astrologerName,
    this.sessionId,
    this.isVideo = false,
  });

  static const route = '/call';

  final int astrologerId;
  final String astrologerName;
  final String? sessionId;
  final bool isVideo;

  @override
  State<AudioCallScreen> createState() => _AudioCallScreenState();
}

class _AudioCallScreenState extends State<AudioCallScreen> {
  Timer? _ticker;
  Duration _elapsed = Duration.zero;
  String? _sessionId;
  String? _liveKitToken;
  LiveKitCallController? _rtc;
  Object? _error;
  bool _active = false;
  bool _ending = false;

  @override
  void initState() {
    super.initState();
    _open();
  }

  @override
  void dispose() {
    _ticker?.cancel();
    _rtc?.dispose();
    super.dispose();
  }

  Future<void> _open() async {
    try {
      final session = context.read<AppSession>();
      _sessionId = widget.sessionId;
      _sessionId ??= await AstrologerApi.instance.addCallRequest(
        astrologerId: widget.astrologerId,
        userId: session.user?.id ?? 0,
        isVideo: widget.isVideo,
      );
      if (_sessionId != null) {
        final info = await AstrologerApi.instance.liveKitSession(
          sessionId: _sessionId!,
          identity: 'user_${session.user?.id ?? 0}',
          displayName: session.user?.name,
        );
        _liveKitToken = info?.token;
        if (info != null) {
          final rtc = LiveKitCallController();
          rtc.addListener(_onRtc);
          _rtc = rtc;
          await rtc.connect(
              token: info.token, wsUrl: info.wsUrl, video: widget.isVideo);
        }
      }
      if (!mounted) return;
      setState(() => _active = true);
      _ticker = Timer.periodic(const Duration(seconds: 1), (_) {
        if (mounted) setState(() => _elapsed += const Duration(seconds: 1));
      });
    } catch (e) {
      if (mounted) setState(() => _error = e);
    }
  }

  void _onRtc() {
    if (mounted) setState(() {});
  }

  String get _clock {
    final m = _elapsed.inMinutes.remainder(60).toString().padLeft(2, '0');
    final s = _elapsed.inSeconds.remainder(60).toString().padLeft(2, '0');
    return '${_elapsed.inHours.toString().padLeft(2, '0')}:$m:$s';
  }

  Future<void> _end() async {
    if (_ending) return;
    setState(() => _ending = true);
    _ticker?.cancel();
    try {
      await _rtc?.leave();
    } catch (_) {/* best effort */}
    try {
      if (_sessionId != null) {
        await AstrologerApi.instance.endCall(callId: _sessionId!);
      }
    } catch (_) {/* server best-effort */}
    if (!mounted) return;
    Navigator.of(context).pop();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.brandDeep,
      body: SafeArea(
        child: _error != null ? _errorView() : _callView(),
      ),
    );
  }

  Widget _callView() {
    final rtc = _rtc;
    final showVideo =
        widget.isVideo && rtc?.remoteVideoTrack != null;
    return Column(
      children: [
        if (showVideo)
          Expanded(
            child: ClipRRect(
              borderRadius: BorderRadius.circular(20),
              child: lk.VideoTrackRenderer(rtc!.remoteVideoTrack!),
            ),
          )
        else
          const Spacer(),
        const Icon(Icons.self_improvement, color: Colors.white, size: 64),
        const SizedBox(height: 18),
        Text(
          rtc?.remoteName ?? widget.astrologerName,
          style: const TextStyle(
              color: Colors.white, fontSize: 22, fontWeight: FontWeight.w700),
        ),
        const SizedBox(height: 8),
        Text(
          rtc?.connected == true
              ? 'LiveKit connected · secure WebRTC'
              : rtc?.connecting == true
                  ? 'Connecting to LiveKit…'
                  : _active
                      ? 'Call connected · ${widget.isVideo ? 'video' : 'audio'}'
                      : 'Connecting…',
          style:
              TextStyle(color: Colors.white.withValues(alpha: 0.7), fontSize: 13),
        ),
        const SizedBox(height: 22),
        Text(
          _clock,
          style: const TextStyle(
              color: Colors.white,
              fontSize: 34,
              fontWeight: FontWeight.w800,
              letterSpacing: 1.5),
        ),
        if (!showVideo) const Spacer(),
        const SizedBox(height: 14),
        Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            _roundAction(
              icon: rtc?.micMuted == true
                  ? Icons.mic_off_rounded
                  : Icons.mic_rounded,
              onTap: rtc?.connected == true ? () => rtc!.toggleMic() : null,
            ),
            if (widget.isVideo) ...[
              const SizedBox(width: 16),
              _roundAction(
                icon: rtc?.camEnabled == true
                    ? Icons.videocam_rounded
                    : Icons.videocam_off_rounded,
                onTap: rtc?.connected == true ? () => rtc!.toggleCam() : null,
              ),
            ],
            const SizedBox(width: 16),
            IconButton(
              style: IconButton.styleFrom(
                backgroundColor: Colors.redAccent,
                padding: const EdgeInsets.all(18),
              ),
              onPressed: _ending ? null : _end,
              icon: const Icon(Icons.call_end_rounded, color: Colors.white, size: 30),
            ),
          ],
        ),
        const SizedBox(height: 28),
        Text(
          _liveKitToken == null
              ? 'LiveKit endpoint pending on server (docs 05 §5.3).\n'
                  'Session + billing timer already live.'
              : '',
          textAlign: TextAlign.center,
          style: TextStyle(color: Colors.white.withValues(alpha: 0.55), fontSize: 11),
        ),
        const SizedBox(height: 16),
      ],
    );
  }

  Widget _roundAction({required IconData icon, VoidCallback? onTap}) {
    return IconButton(
      style: IconButton.styleFrom(
        backgroundColor: Colors.white.withValues(alpha: 0.12),
        foregroundColor: Colors.white,
        padding: const EdgeInsets.all(14),
      ),
      onPressed: onTap,
      icon: Icon(icon, size: 24),
    );
  }

  Widget _errorView() {
    return Column(
      mainAxisAlignment: MainAxisAlignment.center,
      children: [
        const Icon(Icons.error_outline, color: Colors.white, size: 44),
        const SizedBox(height: 12),
        Padding(
          padding: const EdgeInsets.symmetric(horizontal: 32),
          child: Text(
            _error.toString(),
            textAlign: TextAlign.center,
            style: const TextStyle(color: Colors.white),
          ),
        ),
        const SizedBox(height: 16),
        OutlinedButton(
          style: OutlinedButton.styleFrom(
            foregroundColor: Colors.white,
            side: const BorderSide(color: Colors.white),
          ),
          onPressed: () => Navigator.of(context).pop(),
          child: const Text('Back'),
        ),
      ],
    );
  }
}
