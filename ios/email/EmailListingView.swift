import SwiftUI

struct EmailListingView: View {
    let group: String
    
    @State var emails: [Email] = []
    
    var body: some View {
        NavigationStack {
            List() {
                ForEach(emails) { email in
                    NavigationLink(destination: EmailView(uuid: email.uuid)) {
                        EmailRow(email: email)
                    }
                }
            }
        }
        
        .task {
            await getEmails()
        }
    }
    
    func getEmails() async {
        do {
            let decoded: EmailListResponse = try await SeverApi.get(endpoint: "mailbox/\(group)")
            emails = decoded.data
        } catch {
            print(">>> Decode failed: \(error)")
        }
    }
}

struct EmailRow: View {
    let email: Email
    
    var body: some View {
        HStack() {
            AsyncImage(url: email.sender.imageURL) { phase in
                switch phase {
                case .success(let image):
                    image.resizable()
                case .failure:
                    Image(systemName: "person.fill").resizable()
                @unknown default:
                    Image(systemName: "person.fill").resizable()
                }
            }
            .scaledToFit()
            .frame(width: 40, height: 40)
            .clipShape(Circle())
            
            VStack(alignment: .leading) {
                Text(email.subject)
                    .font(.headline)
                Text(email.senderName)
            }
        }
    }
}
