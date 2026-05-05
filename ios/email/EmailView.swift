import SwiftUI
import WebKit

struct EmailView: View {
    let uuid: String
    @State var email: Email?
    
    let html_body = "<h1>Hello, world!</h1>"
    
    @State var webpage = WebPage()
    
    var body: some View {
        Text("Email view")
        
            .task {
                await getEmail()
            }
        
//        We want to render the HTML
        WebView(webpage)
            .task {
                await getEmail()
                if let body = email?.body {
                    webpage.load(html: body)
                }
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
            print(self.email?.body)
        } catch {
            print(error)
        }
    }
}
