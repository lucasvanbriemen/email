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
            await getGroups()
        }
    }

    func getGroups() async {
        do {
            groups = try await SeverApi.get(endpoint: "mailbox/metadata")
        } catch {
            print("something went wrong")
        }
    }
}

#Preview {
    ContentView()
}
