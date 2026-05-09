import Foundation

public struct Email: Identifiable, Codable {
    public var id: String { uuid }
    let uuid: String
    let subject: String
    let senderName: String
    let sender: Sender
    let body: String?
    let created_at: Date?
    
    enum CodingKeys: String, CodingKey {
        case uuid, subject, sender, created_at
        case body = "html_body"
        case senderName = "sender_name"
    }
}

public struct Sender: Codable {
    let name: String
    let email: String
    let imagePath: String?

    enum CodingKeys: String, CodingKey {
        case name, email
        case imagePath = "image_path"
    }

    var imageURL: URL? {
        guard let path = imagePath else { return nil }
        return URL(string: "https://email.lucasvanbriemen.nl/\(path)")
    }
}

struct EmailListResponse: Decodable {
    let data: [Email]
}
