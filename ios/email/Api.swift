import Foundation

class Api {
    private let API_KEY: String = Secrets.devToken
    private let BASE_URL: String = Secrets.baseURL
    
    public func get<T: Decodable>(endpoint: String = "") async throws -> T {
        return try await self.makeRequest(method: "GET", path: endpoint, body: nil)
    }
    
    private func makeRequest<T: Decodable>(method: String, path: String, body: Data?) async throws -> T {
        let url = URL(string: "\(self.BASE_URL)/\(path)")
        
        var request = URLRequest(url: url!)
        request.setValue("Bearer \(self.API_KEY)", forHTTPHeaderField: "Authorization")

        let (data, _) = try await URLSession.shared.data(for: request)
        return try JSONDecoder().decode(T.self, from: data)
    }
}
