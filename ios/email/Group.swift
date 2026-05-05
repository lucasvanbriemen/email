import Foundation

struct Group: Identifiable, Decodable {
    var id: String { path }
    let path: String
    let name: String
    let icon: String
    
    enum CodingKeys: String, CodingKey {
        case path
        case name
        case icon = "ios_icon"
    }
}
