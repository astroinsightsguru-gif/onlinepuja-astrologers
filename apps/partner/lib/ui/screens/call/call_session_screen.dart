import 'dart:async';

import 'package:flutter/material.dart';
import 'package:livekit_client/livekit_client.dart' as lk;
import 'package:op_shared/op_shared.dart';
import 'package:provider/provider.dart';

import '../../../state/app_session.dart';

/// Partner-side audio/video call session.
///
/// Uses a single **LiveKit** path (free self-hosted WebRTC, docs 04 §4.2).
/// The backend mints a LiveKit token via `POST /api/livekit/token`. If the
/// endpoint is not yet deployed this screen degrades to a REST session with
/// a live billing timer so the flow stays testable end-to-end.
class CallSessionScreen extends StatefulWidget {
  const CallSessionScreen({
    super.key,
    required this.customerId,
    required this.customerName,
    this.sessionId,
    this.isVideo = false,
  });

  static const route = '/call';

  final int customerId;
  final String customerName;
  final String? sessionId;
  final bool isVideo;

  @override
  State<CallSessionScreen> createState() => _CallSessionScreenState();
}

class _CallSessionScreenState extends State<CallSessionScreen> {
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
      final session = context.read<PartnerSession>();
      if (!session.isLoggedIn || session.astrologerId == 0) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(content: Text('Partner session expired. Please log in.')),
          );
          Navigator.of(context).pop();
        }
        return;
      }
      _sessionId = widget.sessionId;
      _sessionId ??= await AstrologerApi.instance.addCallRequest(
        astrologerId: session.astrologerId,
        userId: widget.customerId,
        isVideo: widget.isVideo,
      );
      if (_sessionId != null) {
        final info = await AstrologerApi.instance.liveKitSession(
          sessionId: _sessionId!,
          identity: 'astro_${session.astrologerId}',
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
    final showRemoteVideo = widget.isVideo && rtc?.remoteVideoTrack != null;
    final showLocalVideo = widget.isVideo && rtc?.camEnabled == true && rtc?.localVideoTrack != null;

    if (widget.isVideo) {
      return Stack(
        children: [
          // 1. Remote Client Video
          Positioned.fill(
            child: showRemoteVideo
                ? lk.VideoTrackRenderer(rtc!.remoteVideoTrack!)
                : Container(
                    decoration: BoxDecoration(
                      gradient: RadialGradient(
                        colors: [AppTheme.brandDeep, Colors.black],
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
                            child: const Icon(Icons.person, color: Colors.white, size: 52),
                          ),
                          const SizedBox(height: 16),
                          Text(
                            rtc?.remoteName ?? widget.customerName,
                            style: const TextStyle(color: Colors.white, fontSize: 22, fontWeight: FontWeight.w700),
                          ),
                          const SizedBox(height: 6),
                          Text(
                            rtc?.connected == true ? 'Waiting for client video…' : 'Connecting to LiveKit…',
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
                IconButton(
                  style: IconButton.styleFrom(
                    backgroundColor: Colors.black.withValues(alpha: 0.45),
                    foregroundColor: Colors.white,
                  ),
                  tooltip: 'Client Info',
                  icon: const Icon(Icons.info_outline_rounded, size: 20),
                  onPressed: _showClientInfoSheet,
                ),
              ],
            ),
          ),

          // 3. Floating Picture-in-Picture Astrologer Preview
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
                    'LiveKit session pending · consultation timer active',
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
          child: const Icon(Icons.person, color: Colors.white, size: 56),
        ),
        const SizedBox(height: 18),
        Text(
          rtc?.remoteName ?? widget.customerName,
          style: const TextStyle(color: Colors.white, fontSize: 22, fontWeight: FontWeight.w700),
        ),
        const SizedBox(height: 8),
        Text(
          rtc?.connected == true
              ? 'LiveKit connected · HD Audio'
              : rtc?.connecting == true
                  ? 'Connecting to LiveKit…'
                  : _active
                      ? 'Consultation in progress'
                      : 'Connecting…',
          style: TextStyle(color: Colors.white.withValues(alpha: 0.7), fontSize: 13),
        ),
        const SizedBox(height: 22),
        Text(
          _clock,
          style: const TextStyle(
              color: Colors.white, fontSize: 34, fontWeight: FontWeight.w800, letterSpacing: 1.5),
        ),
        const Spacer(),
        Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            _roundAction(
              icon: rtc?.micMuted == true ? Icons.mic_off_rounded : Icons.mic_rounded,
              onTap: rtc?.connected == true ? () => rtc!.toggleMic() : null,
            ),
            const SizedBox(width: 14),
            _roundAction(
              icon: Icons.info_outline_rounded,
              onTap: _showClientInfoSheet,
            ),
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
                  'Consultation timer active.'
              : '',
          textAlign: TextAlign.center,
          style: TextStyle(color: Colors.white.withValues(alpha: 0.55), fontSize: 11),
        ),
        const SizedBox(height: 16),
      ],
    );
  }

  void _showClientInfoSheet() {
    showModalBottomSheet(
      context: context,
      backgroundColor: AppTheme.brandDeep,
      isScrollControlled: true,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      builder: (ctx) => Padding(
        padding: const EdgeInsets.all(20),
        child: FutureBuilder<Map<String, dynamic>?>(
          future: AstrologerApi.instance.getIntakeForm(userId: widget.customerId),
          builder: (context, snapshot) {
            final intake = snapshot.data;
            final dob = intake?['birthDate']?.toString() ?? 'Not specified';
            final tob = intake?['birthTime']?.toString() ?? 'Not specified';
            final pob = intake?['birthPlace']?.toString() ?? 'Not specified';
            final topic = intake?['topicOfConcern']?.toString() ?? 'General Consultation';
            final occupation = intake?['occupation']?.toString();

            return Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    const Icon(Icons.account_circle, color: Colors.amberAccent, size: 28),
                    const SizedBox(width: 10),
                    Expanded(
                      child: Text(
                        widget.customerName,
                        style: const TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold),
                      ),
                    ),
                    IconButton(
                      icon: const Icon(Icons.close, color: Colors.white70),
                      onPressed: () => Navigator.pop(ctx),
                    ),
                  ],
                ),
                const Divider(color: Colors.white24),
                const SizedBox(height: 8),
                Text('Client ID: #${widget.customerId}', style: const TextStyle(color: Colors.white70)),
                const SizedBox(height: 4),
                Text('Session Type: ${widget.isVideo ? '1:1 Video Consultation' : '1:1 Audio Consultation'}',
                    style: const TextStyle(color: Colors.white70)),
                const SizedBox(height: 4),
                Text('Elapsed Time: $_clock', style: const TextStyle(color: Colors.amberAccent, fontWeight: FontWeight.bold)),
                const SizedBox(height: 12),
                const Text('Kundli & Birth Details:', style: TextStyle(color: Colors.white, fontWeight: FontWeight.w600)),
                const SizedBox(height: 6),
                Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: Colors.white.withValues(alpha: 0.08),
                    borderRadius: BorderRadius.circular(10),
                  ),
                  child: Column(
                    children: [
                      _infoRow('Date of Birth', dob),
                      const SizedBox(height: 4),
                      _infoRow('Time of Birth', tob),
                      const SizedBox(height: 4),
                      _infoRow('Place of Birth', pob),
                      const SizedBox(height: 4),
                      _infoRow('Concern', topic),
                      if (occupation != null && occupation.isNotEmpty) ...[
                        const SizedBox(height: 4),
                        _infoRow('Occupation', occupation),
                      ],
                    ],
                  ),
                ),
                const SizedBox(height: 14),
                AiCopilotCard(
                  clientName: widget.customerName,
                  dob: dob,
                  tob: tob,
                  pob: pob,
                  concern: topic,
                ),
                const SizedBox(height: 16),
              ],
            );
          },
        ),
      ),
    );
  }

  Widget _infoRow(String label, String value) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(label, style: const TextStyle(color: Colors.white70, fontSize: 13)),
        Text(value, style: const TextStyle(color: Colors.white, fontSize: 13, fontWeight: FontWeight.w600)),
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
