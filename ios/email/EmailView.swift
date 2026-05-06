import SwiftUI
import WebKit

struct EmailView: View {
    let uuid: String
    @State private var email: Email?
    @State private var webpage: WebPage

    init(uuid: String) {
        self.uuid = uuid
        _webpage = State(initialValue: WebPage(navigationDecider: EmailNavigationDecider()))
    }

    var body: some View {
        WebView(webpage)
            .task {
                await getEmail()
                guard let email else { return }
                
                webpage.load(html: email.body!)
                print(email.sender.email)
            }
    }

    func getEmail() async {
        guard let url = URL(string: "\(Secrets.baseURL)/email/\(uuid)") else { return }
        var request = URLRequest(url: url)
        request.setValue("Bearer \(Secrets.devToken)", forHTTPHeaderField: "Authorization")

        do {
            let (data, _) = try await URLSession.shared.data(for: request)
            self.email = try JSONDecoder().decode(Email.self, from: data)
        } catch {
            print(error)
        }
    }
}

struct EmailNavigationDecider: WebPage.NavigationDeciding {
    func decidePolicy(
        for action: WebPage.NavigationAction,
        preferences: inout WebPage.NavigationPreferences
    ) async -> WKNavigationActionPolicy {
        // For email content: open external links in Safari instead of in-place
        if action.navigationType == .linkActivated,
           let url = action.request.url {
            await UIApplication.shared.open(url)
            return .cancel
        }
        return .allow
    }
}
