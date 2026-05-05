import SwiftUI

struct ContentView: View {
    @State var groups: [Group] = []
    
    var body: some View {
        TabView {
            ForEach(groups) { group in
                Tab(group.name, systemImage: group.icon) {
                    EmailListingView(group: group.path)
                }
            }
        }
        .task {
            await getEmails()
        }
    }

    func getEmails() async {
        let url = URL(string: "https://email.lucasvanbriemen.nl/api/mailbox/metadata")!
        let devToken = "DEV_TOKEN"
        
        var request = URLRequest(url: url)
        request.setValue("Bearer \(devToken)", forHTTPHeaderField: "Authorization")

        do {
            let (data, _) = try await URLSession.shared.data(for: request)
            groups = try JSONDecoder().decode([Group].self, from: data)
        } catch {
            print("something went wrong")
        }
    }
}

#Preview {
    ContentView()
}

