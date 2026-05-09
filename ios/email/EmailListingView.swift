import SwiftUI

struct EmailListingView: View {
    let group: String
    
    @State var emails: [Email] = []
    @State var isLoading: Bool = true
    
    var body: some View {
        NavigationStack {
            List() {
                ForEach(sortedEmailsByDate(), id: \.0) { (day, dayEmails) in
                    Section(header: Text(Self.dayFormatter.string(from: day))) {
                        ForEach(dayEmails) { email in
                            NavigationLink(destination: EmailView(uuid: email.uuid)) {
                                EmailRow(email: email)
                            }
                            .padding(EdgeInsets(top: 10, leading: 0, bottom: 10, trailing: 0))
                        }
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
            isLoading = false
        } catch {
            print(">>> Decode failed: \(error)")
        }
    }
    
    func sortedEmailsByDate() -> [(Date, [Email])] {
        var groups: [Date: [Email]] = [:]
        let calendar = Calendar.current

        for email in emails {
            guard let date = email.created_at else { continue }
            let dayKey = calendar.startOfDay(for: date)
            groups[dayKey, default: []].append(email)
        }

        return groups.sorted { $0.key > $1.key }
    }

    static let dayFormatter: DateFormatter = {
        let formatter = DateFormatter()
        formatter.dateFormat = "EEE MMM d yyyy"
        return formatter
    }()
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
