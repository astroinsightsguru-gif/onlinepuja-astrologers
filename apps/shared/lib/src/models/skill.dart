/// Astrologer skill / category row (`getSkill` responses).
class Skill {
  Skill({this.id, this.name, this.image, this.status});

  final int? id;
  final String? name;
  final String? image;
  final String? status;

  Skill.fromJson(Map<String, dynamic> json)
      : id = int.tryParse(json['id']?.toString() ?? ''),
        name = json['name'] ?? '',
        image = json['image'] ?? '',
        status = json['status'] ?? '';

  Map<String, dynamic> toJson() => {
        'id': id,
        'name': name,
        'image': image,
        'status': status,
      };
}
