import Foundation

class SeverApi {
    private static let API_KEY: String = Secrets.devToken
    private static let BASE_URL: String = Secrets.baseURL
    
    public static func get<T: Decodable>(endpoint: String = "") async throws -> T {
        return try await makeRequest(method: "GET", path: endpoint, body: nil)
    }
    
    private static func makeRequest<T: Decodable>(method: String, path: String, body: Data?) async throws -> T {
        let url = URL(string: "\(BASE_URL)/\(path)")
        
        var request = URLRequest(url: url!)
        request.setValue("Bearer \(API_KEY)", forHTTPHeaderField: "Authorization")

        let (data, _) = try await URLSession.shared.data(for: request)
        return try decoder.decode(T.self, from: data)
    }

    private static let decoder: JSONDecoder = {
        let decoder = JSONDecoder()
        decoder.dateDecodingStrategy = .custom { decoder in
            let container = try decoder.singleValueContainer()
            let dateStr = try container.decode(String.self)

            let formatter = ISO8601DateFormatter()
            formatter.formatOptions = [.withInternetDateTime, .withFractionalSeconds]
            if let date = formatter.date(from: dateStr) { return date }

            formatter.formatOptions = [.withInternetDateTime]
            if let date = formatter.date(from: dateStr) { return date }

            throw DecodingError.dataCorruptedError(
                in: container,
                debugDescription: "Invalid date: \(dateStr)"
            )
        }
        return decoder
    }()
}
