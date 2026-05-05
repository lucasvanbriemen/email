import Foundation

struct Group: Identifiable, Decodable {
    var id: String { path }
    let path: String
    let name: String
    let image: String
    
    enum CodingKeys: String, CodingKey {
        case path
        case name
        case image = "ios_icon"
    }
}
