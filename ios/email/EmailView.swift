import SwiftUI

struct EmailView: View {
    let uuid: String
    @State var email: Email?
    
    var body: some View {
        Text("Email view")
        
            .task {
                await getEmail()
            }
    }
    
    func getEmail() async {
        let url = URL(string: "\(Secrets.baseURL)/email/\(uuid)")
        
        var request = URLRequest(url: url!)
        request.setValue("Bearer \(Secrets.devToken)", forHTTPHeaderField: "Authorization")
        
        do {
            let (data, _) = try await URLSession.shared.data(for: request)
            
            let decoder = JSONDecoder()
            self.email = try? decoder.decode(Email.self, from: data)
            print(self.email?.subject)
        } catch {
            print(error)
        }
    }
}
