/// Report / blog / story / gift / address misc models mirroring the legacy
/// `reportModel.dart`, `blogModel.dart`, `Allstories.dart`, `getGift` and
/// `getOrderAddress` response contracts.
library;

/// Report type from `/getReportType`.
class ReportType {
  ReportType({
    this.id,
    this.name,
    this.description,
    this.price,
    this.image,
    this.isActive,
  });

  dynamic id, name, description, price, image, isActive;

  factory ReportType.fromJson(Map<String, dynamic> json) => ReportType(
        id: json['id'],
        name: json['name'] ?? json['reportType'],
        description: json['description'],
        price: json['price'],
        image: json['image'],
        isActive: json['isActive'],
      );

  double get priceValue => double.tryParse(price?.toString() ?? '') ?? 0.0;
}

/// Blog from the blog listing endpoints.
class Blog {
  Blog({
    this.id,
    this.title,
    this.slug,
    this.description,
    this.image,
    this.author,
    this.createdAt,
  });

  dynamic id, title, slug, description, image, author, createdAt;

  factory Blog.fromJson(Map<String, dynamic> json) => Blog(
        id: json['id'],
        title: json['title'] ?? json['blogTitle'],
        slug: json['slug'],
        description: json['description'] ?? json['blogDescription'],
        image: json['image'] ?? json['blogImage'],
        author: json['author'] ?? json['writerName'],
        createdAt: json['created_at'] ?? json['createdAt'],
      );
}

/// Story slide from `/getStory` / `/getAstrologerStory`.
class Story {
  Story({
    this.id,
    this.title,
    this.image,
    this.link,
    this.astrologerId,
    this.clickCount,
  });

  dynamic id, title, image, link, astrologerId, clickCount;

  factory Story.fromJson(Map<String, dynamic> json) => Story(
        id: json['id'],
        title: json['title'] ?? json['storyTitle'],
        image: json['image'] ?? json['storyImage'],
        link: json['link'] ?? json['storyLink'],
        astrologerId: json['astrologerId'],
        clickCount: json['clickCount'],
      );
}

/// Gift item from `/getGift`.
class Gift {
  Gift({this.id, this.name, this.image, this.price, this.isActive});

  dynamic id, name, image, price, isActive;

  factory Gift.fromJson(Map<String, dynamic> json) => Gift(
        id: json['id'],
        name: json['name'] ?? json['giftName'],
        image: json['image'] ?? json['giftImage'],
        price: json['price'],
        isActive: json['isActive'],
      );

  double get priceValue => double.tryParse(price?.toString() ?? '') ?? 0.0;
}

/// Saved order address from `/getOrderAddress`.
class OrderAddress {
  OrderAddress({
    this.id,
    this.name,
    this.phone,
    this.addressLine1,
    this.addressLine2,
    this.city,
    this.state,
    this.pincode,
    this.country,
    this.isDefault,
  });

  dynamic id, name, phone, addressLine1, addressLine2;
  dynamic city, state, pincode, country, isDefault;

  factory OrderAddress.fromJson(Map<String, dynamic> json) => OrderAddress(
        id: json['id'],
        name: json['name'],
        phone: json['phone'] ?? json['contactNo'],
        addressLine1: json['addressLine1'],
        addressLine2: json['addressLine2'],
        city: json['city'],
        state: json['state'],
        pincode: json['pincode'],
        country: json['country'],
        isDefault: json['isDefault'],
      );

  String get fullAddress {
    final parts = <String>[
      if (addressLine1 != null) addressLine1.toString(),
      if (addressLine2 != null) addressLine2.toString(),
      if (city != null) city.toString(),
      if (state != null) state.toString(),
      if (pincode != null) pincode.toString(),
      if (country != null) country.toString(),
    ];
    return parts.join(', ');
  }
}
