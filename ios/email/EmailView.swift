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
                
                if email.sender.email == "ntfy@ltvb.nl" {
                    let url = URL(string: email.body!)!

                    if let host = url.host,
                       let cookie = HTTPCookie(properties: [
                           .domain: host,
                           .path: "/",
                           .name: "auth_token",
                           .value: Secrets.devToken,
                       ]) {
                        await WKWebsiteDataStore.default().httpCookieStore.setCookie(cookie)
                    }

                    webpage.load(URLRequest(url: url))
                } else {
                    webpage.load(html: email.body!)
                }
            }
    }
    func getEmail() async {
        do {
            email = try await SeverApi.get(endpoint: "email/\(uuid)")
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
