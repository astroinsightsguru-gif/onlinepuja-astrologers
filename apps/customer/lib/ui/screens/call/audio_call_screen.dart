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
    this.ratePerMinute = 15.0,
    this.sessionId,
    this.isVideo = false,
  });

  static const route = '/call';

  final int astrologerId;
  final String astrologerName;
  final double ratePerMinute;
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
      if (!session.isAuthenticated || session.user == null) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(content: Text('Please log in to start a consultation.')),
          );
          Navigator.of(context).pop();
        }
        return;
      }
      _sessionId = widget.sessionId;
      _sessionId ??= await AstrologerApi.instance.addCallRequest(
        astrologerId: widget.astrologerId,
        userId: session.userId,
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
        if (!mounted) return;
        setState(() => _elapsed += const Duration(seconds: 1));

        final session = context.read<AppSession>();
        final balance = session.user?.walletAmount ?? 0;
        final rate = widget.ratePerMinute > 0 ? widget.ratePerMinute : 15.0;
        final totalAllowedSecs = ((balance / rate) * 60).floor();
        final remainingSecs = totalAllowedSecs - _elapsed.inSeconds;

        if (remainingSecs <= 0 && !_ending) {
          _end();
          showSnack(context, 'Consultation ended: Wallet balance exhausted.', error: true);
        }
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
    showConsultationFeedbackDialog(
      context: context,
      astrologerId: widget.astrologerId,
      astrologerName: widget.astrologerName,
    );
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
    final showRemoteVideo = widget.isVideo && rtc?.remoteVideoTrack != null;
    final showLocalVideo = widget.isVideo && rtc?.camEnabled == true && rtc?.localVideoTrack != null;

    if (widget.isVideo) {
      return Stack(
        children: [
          // 1. Full-bleed remote video or dark placeholder
          Positioned.fill(
            child: showRemoteVideo
                ? lk.VideoTrackRenderer(rtc!.remoteVideoTrack!)
                : Container(
                    decoration: BoxDecoration(
                      gradient: RadialGradient(
                        colors: [AppTheme.brandSaffron.withValues(alpha: 0.2), AppTheme.brandDeep],
                        radius: 1.2,
                      ),
                    ),
                    child: Center(
                      child: Column(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          CircleAvatar(
                            radius: 46,
                            backgroundColor: Colors.white.withValues(alpha: 0.15),
                            child: const Icon(Icons.self_improvement, color: Colors.white, size: 52),
                          ),
                          const SizedBox(height: 16),
                          Text(
                            rtc?.remoteName ?? widget.astrologerName,
                            style: const TextStyle(color: Colors.white, fontSize: 22, fontWeight: FontWeight.w700),
                          ),
                          const SizedBox(height: 6),
                          Text(
                            rtc?.connected == true ? 'Waiting for astrologer video…' : 'Connecting to LiveKit…',
                            style: TextStyle(color: Colors.white.withValues(alpha: 0.7), fontSize: 13),
                          ),
                        ],
                      ),
                    ),
                  ),
          ),

          // 2. Top Bar (Overlay)
          Positioned(
            top: 12,
            left: 16,
            right: 16,
            child: Row(
              children: [
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
                  decoration: BoxDecoration(
                    color: Colors.black.withValues(alpha: 0.5),
                    borderRadius: BorderRadius.circular(20),
                    border: Border.all(color: Colors.white24),
                  ),
                  child: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Container(
                        width: 8,
                        height: 8,
                        decoration: BoxDecoration(
                          shape: BoxShape.circle,
                          color: rtc?.connected == true ? Colors.greenAccent : Colors.amberAccent,
                        ),
                      ),
                      const SizedBox(width: 8),
                      Text(
                        _clock,
                        style: const TextStyle(color: Colors.white, fontSize: 13, fontWeight: FontWeight.w700),
                      ),
                    ],
                  ),
                ),
                const Spacer(),
                if (showRemoteVideo)
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                    decoration: BoxDecoration(
                      color: Colors.black.withValues(alpha: 0.45),
                      borderRadius: BorderRadius.circular(16),
                    ),
                    child: Text(
                      widget.astrologerName,
                      style: const TextStyle(color: Colors.white, fontSize: 12, fontWeight: FontWeight.w600),
                    ),
                  ),
              ],
            ),
          ),

          // 3. Floating Picture-in-Picture Local Camera Preview
          Positioned(
            top: 60,
            right: 16,
            child: Container(
              width: 105,
              height: 150,
              decoration: BoxDecoration(
                color: Colors.black87,
                borderRadius: BorderRadius.circular(14),
                border: Border.all(color: Colors.white30, width: 1.5),
                boxShadow: const [BoxShadow(color: Colors.black54, blurRadius: 10)],
              ),
              child: ClipRRect(
                borderRadius: BorderRadius.circular(12),
                child: showLocalVideo
                    ? lk.VideoTrackRenderer(rtc!.localVideoTrack!)
                    : Center(
                        child: Column(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            const Icon(Icons.videocam_off_rounded, color: Colors.white54, size: 28),
                            const SizedBox(height: 4),
                            Text(
                              'Camera off',
                              style: TextStyle(color: Colors.white.withValues(alpha: 0.6), fontSize: 10),
                            ),
                          ],
                        ),
                      ),
              ),
            ),
          ),

          // 4. Bottom Controls
          Positioned(
            bottom: 24,
            left: 0,
            right: 0,
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    _roundAction(
                      icon: rtc?.micMuted == true ? Icons.mic_off_rounded : Icons.mic_rounded,
                      onTap: rtc?.connected == true ? () => rtc!.toggleMic() : null,
                    ),
                    const SizedBox(width: 14),
                    _roundAction(
                      icon: rtc?.camEnabled == true ? Icons.videocam_rounded : Icons.videocam_off_rounded,
                      onTap: rtc?.connected == true ? () => rtc!.toggleCam() : null,
                    ),
                    const SizedBox(width: 14),
                    _roundAction(
                      icon: Icons.flip_camera_ios_rounded,
                      onTap: (rtc?.connected == true && rtc?.camEnabled == true)
                          ? () => rtc!.switchCamera()
                          : null,
                    ),
                    const SizedBox(width: 18),
                    IconButton(
                      style: IconButton.styleFrom(
                        backgroundColor: Colors.redAccent,
                        padding: const EdgeInsets.all(16),
                      ),
                      onPressed: _ending ? null : _end,
                      icon: const Icon(Icons.call_end_rounded, color: Colors.white, size: 30),
                    ),
                  ],
                ),
                if (_liveKitToken == null) ...[
                  const SizedBox(height: 12),
                  Text(
                    'LiveKit session pending · billing timer active',
                    style: TextStyle(color: Colors.white.withValues(alpha: 0.6), fontSize: 11),
                  ),
                ],
              ],
            ),
          ),
        ],
      );
    }

    // Audio Call View
    return Column(
      children: [
        const Spacer(),
        CircleAvatar(
          radius: 48,
          backgroundColor: Colors.white.withValues(alpha: 0.12),
          child: const Icon(Icons.self_improvement, color: Colors.white, size: 56),
        ),
        const SizedBox(height: 18),
        Text(
          rtc?.remoteName ?? widget.astrologerName,
          style: const TextStyle(color: Colors.white, fontSize: 22, fontWeight: FontWeight.w700),
        ),
        const SizedBox(height: 8),
        Text(
          rtc?.connected == true
              ? 'LiveKit connected · HD Audio'
              : rtc?.connecting == true
                  ? 'Connecting to LiveKit…'
                  : _active
                      ? 'Call in progress'
                      : 'Connecting…',
          style: TextStyle(color: Colors.white.withValues(alpha: 0.7), fontSize: 13),
        ),
        const SizedBox(height: 22),
        Text(
          _clock,
          style: const TextStyle(
              color: Colors.white, fontSize: 34, fontWeight: FontWeight.w800, letterSpacing: 1.5),
        ),
        const SizedBox(height: 10),
        Builder(
          builder: (ctx) {
            final session = ctx.watch<AppSession>();
            final balance = session.user?.walletAmount ?? 0;
            const rate = 15.0;
            final totalAllowedSecs = ((balance / rate) * 60).floor();
            final remainingSecs = (totalAllowedSecs - _elapsed.inSeconds).clamp(0, 99999);
            final remMin = (remainingSecs / 60).floor();
            final remSec = (remainingSecs % 60).toString().padLeft(2, '0');
            final isLow = remainingSecs < 120;

            return Container(
              padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 6),
              decoration: BoxDecoration(
                color: isLow ? Colors.red.withValues(alpha: 0.25) : Colors.black.withValues(alpha: 0.35),
                borderRadius: BorderRadius.circular(20),
                border: Border.all(color: isLow ? Colors.redAccent : Colors.white24),
              ),
              child: Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Icon(
                    isLow ? Icons.warning_amber_rounded : Icons.account_balance_wallet_outlined,
                    color: isLow ? Colors.redAccent : Colors.amberAccent,
                    size: 16,
                  ),
                  const SizedBox(width: 6),
                  Text(
                    '⏳ $remMin:$remSec remaining (₹${balance.toStringAsFixed(0)})',
                    style: TextStyle(
                      color: isLow ? Colors.redAccent : Colors.white,
                      fontSize: 12,
                      fontWeight: FontWeight.w700,
                    ),
                  ),
                ],
              ),
            );
          },
        ),
        const Spacer(),
        Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            _roundAction(
              icon: rtc?.micMuted == true ? Icons.mic_off_rounded : Icons.mic_rounded,
              onTap: rtc?.connected == true ? () => rtc!.toggleMic() : null,
            ),
            const SizedBox(width: 20),
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
    final isLowBalance = _error != null &&
        (_error.toString().toLowerCase().contains('balance') ||
            _error.toString().toLowerCase().contains('recharge'));

    return Center(
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 32),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(
              isLowBalance ? Icons.account_balance_wallet_outlined : Icons.error_outline,
              color: isLowBalance ? Colors.amberAccent : Colors.white,
              size: 54,
            ),
            const SizedBox(height: 16),
            Text(
              _error is ApiException
                  ? (_error as ApiException).message
                  : _error.toString(),
              textAlign: TextAlign.center,
              style: const TextStyle(color: Colors.white, fontSize: 16, fontWeight: FontWeight.w600),
            ),
            const SizedBox(height: 20),
            if (isLowBalance)
              FilledButton.icon(
                style: FilledButton.styleFrom(backgroundColor: AppTheme.brandSaffron),
                icon: const Icon(Icons.add_card),
                label: const Text('Recharge Wallet'),
                onPressed: () => Navigator.of(context)
                    .pushNamed('/wallet')
                    .then((_) => _open()),
              )
            else
              OutlinedButton(
                style: OutlinedButton.styleFrom(
                  foregroundColor: Colors.white,
                  side: const BorderSide(color: Colors.white),
                ),
                onPressed: () => Navigator.of(context).pop(),
                child: const Text('Back'),
              ),
          ],
        ),
      ),
    );
  }
}
