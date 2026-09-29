import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';

/// Detailed reader view for astrology articles.
class BlogDetailScreen extends StatelessWidget {
  const BlogDetailScreen({super.key, required this.blog});

  final Blog blog;

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    final imageUrl = blog.image?.toString();
    final author = blog.author?.toString() ?? 'Online Puja Team';
    final date = blog.createdAt?.toString() ?? 'Recently published';

    return Scaffold(
      appBar: AppBar(
        title: const Text('Article'),
      ),
      body: ListView(
        padding: const EdgeInsets.fromLTRB(16, 8, 16, 32),
        children: [
          if (imageUrl != null && imageUrl.isNotEmpty) ...[
            ClipRRect(
              borderRadius: BorderRadius.circular(18),
              child: AspectRatio(
                aspectRatio: 16 / 9,
                child: CachedNetworkImage(
                  imageUrl: MiscApi.imageUrl(imageUrl),
                  fit: BoxFit.cover,
                  errorWidget: (context, url, error) => Container(
                    color: scheme.primaryContainer,
                    child: Icon(Icons.auto_stories_rounded, color: scheme.primary, size: 48),
                  ),
                ),
              ),
            ),
            const SizedBox(height: 16),
          ],
          Text(
            blog.title?.toString() ?? 'Astrology Insights',
            style: Theme.of(context).textTheme.headlineSmall?.copyWith(
                  fontWeight: FontWeight.w800,
                  height: 1.3,
                ),
          ),
          const SizedBox(height: 12),
          Row(
            children: [
              CircleAvatar(
                radius: 16,
                backgroundColor: scheme.primaryContainer,
                child: Icon(Icons.person, size: 18, color: scheme.primary),
              ),
              const SizedBox(width: 10),
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    author,
                    style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13),
                  ),
                  Text(
                    date,
                    style: TextStyle(color: Colors.grey.shade600, fontSize: 11),
                  ),
                ],
              ),
            ],
          ),
          const SizedBox(height: 16),
          const Divider(),
          const SizedBox(height: 12),
          Text(
            blog.description?.toString() ?? '',
            style: Theme.of(context).textTheme.bodyLarge?.copyWith(
                  height: 1.65,
                  fontSize: 16,
                  color: Theme.of(context).colorScheme.onSurface.withValues(alpha: 0.9),
                ),
          ),
          const SizedBox(height: 32),
          Card(
            color: scheme.surfaceContainerHighest.withValues(alpha: 0.5),
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
            child: Padding(
              padding: const EdgeInsets.all(16),
              child: Row(
                children: [
                  const Icon(Icons.self_improvement, color: AppTheme.brandSaffron, size: 36),
                  const SizedBox(width: 12),
                  const Expanded(
                    child: Text(
                      'Have personal questions about your horoscope or planetary transit? Talk to an expert astrologer now.',
                      style: TextStyle(fontSize: 13),
                    ),
                  ),
                  const SizedBox(width: 8),
                  FilledButton(
                    onPressed: () => Navigator.of(context).pop(),
                    child: const Text('Consult'),
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }
}
