import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';

import 'blog_detail_screen.dart';

/// Astrology blogs, articles, and Vedic knowledge stream.
class BlogScreen extends StatefulWidget {
  const BlogScreen({super.key});

  static const route = '/blogs';

  @override
  State<BlogScreen> createState() => _BlogScreenState();
}

class _BlogScreenState extends State<BlogScreen> {
  late Future<List<Blog>> _future;
  String _search = '';

  @override
  void initState() {
    super.initState();
    _future = MiscApi.instance.blogs();
  }

  void _reload() => setState(() => _future = MiscApi.instance.blogs());

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    return Scaffold(
      appBar: AppBar(
        title: const Text('Astrology & Spiritual Blogs'),
      ),
      body: Column(
        children: [
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 12, 16, 8),
            child: TextField(
              decoration: InputDecoration(
                hintText: 'Search articles, rituals, horoscopes…',
                prefixIcon: const Icon(Icons.search_rounded),
                contentPadding: const EdgeInsets.symmetric(horizontal: 16),
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(16)),
              ),
              onChanged: (v) => setState(() => _search = v.trim().toLowerCase()),
            ),
          ),
          Expanded(
            child: FutureBuilder<List<Blog>>(
              future: _future,
              builder: (ctx, snap) {
                if (snap.connectionState == ConnectionState.waiting) {
                  return const Center(child: CircularProgressIndicator());
                }
                if (snap.hasError) {
                  return StatusViews.error(
                    context,
                    snap.error!,
                    onRetry: _reload,
                  );
                }
                final list = (snap.data ?? []).where((b) {
                  if (_search.isEmpty) return true;
                  final title = (b.title ?? '').toString().toLowerCase();
                  final desc = (b.description ?? '').toString().toLowerCase();
                  return title.contains(_search) || desc.contains(_search);
                }).toList();

                if (list.isEmpty) {
                  return StatusViews.empty(
                    context,
                    message: _search.isNotEmpty
                        ? 'No articles match your search'
                        : 'Articles will appear here shortly',
                  );
                }

                return RefreshIndicator(
                  onRefresh: () async => _reload(),
                  child: ListView.separated(
                    padding: const EdgeInsets.all(16),
                    itemCount: list.length,
                    separatorBuilder: (_, index) => const SizedBox(height: 16),
                    itemBuilder: (context, i) {
                      final blog = list[i];
                      final imageUrl = blog.image?.toString();
                      return Card(
                        clipBehavior: Clip.antiAlias,
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(18)),
                        elevation: 1.5,
                        child: InkWell(
                          onTap: () {
                            if (blog.id != null) {
                              MiscApi.instance.addBlogReader(blogId: blog.id).catchError((_) {});
                            }
                            Navigator.of(context).push(
                              MaterialPageRoute(builder: (_) => BlogDetailScreen(blog: blog)),
                            );
                          },
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              if (imageUrl != null && imageUrl.isNotEmpty)
                                AspectRatio(
                                  aspectRatio: 16 / 9,
                                  child: CachedNetworkImage(
                                    imageUrl: MiscApi.imageUrl(imageUrl),
                                    fit: BoxFit.cover,
                                    errorWidget: (context, url, error) => Container(
                                      color: scheme.primaryContainer,
                                      child: Icon(Icons.auto_stories_rounded, color: scheme.primary, size: 40),
                                    ),
                                  ),
                                ),
                              Padding(
                                padding: const EdgeInsets.all(16),
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text(
                                      blog.title?.toString() ?? 'Article',
                                      style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 17),
                                      maxLines: 2,
                                      overflow: TextOverflow.ellipsis,
                                    ),
                                    const SizedBox(height: 6),
                                    Text(
                                      blog.description?.toString() ?? '',
                                      style: TextStyle(color: Colors.grey.shade700, fontSize: 13, height: 1.4),
                                      maxLines: 2,
                                      overflow: TextOverflow.ellipsis,
                                    ),
                                    const SizedBox(height: 12),
                                    Row(
                                      children: [
                                        const Icon(Icons.person_outline, size: 14, color: Colors.grey),
                                        const SizedBox(width: 4),
                                        Text(
                                          blog.author?.toString() ?? 'Online Puja Astrologer',
                                          style: const TextStyle(fontSize: 12, color: Colors.grey),
                                        ),
                                        const Spacer(),
                                        Text(
                                          'Read article →',
                                          style: TextStyle(
                                            color: scheme.primary,
                                            fontWeight: FontWeight.bold,
                                            fontSize: 13,
                                          ),
                                        ),
                                      ],
                                    ),
                                  ],
                                ),
                              ),
                            ],
                          ),
                        ),
                      );
                    },
                  ),
                );
              },
            ),
          ),
        ],
      ),
    );
  }
}
